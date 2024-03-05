<?php

namespace John\Reqres\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use John\Reqres\DTOs\UserDTO;
use John\Reqres\Exceptions\ServiceValidationException;
use John\Reqres\Exceptions\ServiceNotAvailableException;

class ReqResService
{
  private Client $client;

  public function __construct(
    private readonly string $baseUrl = 'https://reqres.in',
    ?Client $client = null
  ) {
    $this->client = $client ?? new Client(['base_uri' => $this->baseUrl]);
  }

  public function getUserById(string $id): UserDTO
  {
    $response = $this->request('GET', "/api/users/$id")['data'];

    return new UserDTO(...$response);
  }

  public function getPaginatedUsers(int $pageNumber = 1): array
  {
    $response = $this->request('GET', "/api/users", [
      'query' => [
        'page' => $pageNumber
      ]
    ]);

    return [
      'users' => array_map(fn ($user) => new UserDTO(...$user), $response['data']),
      'page' => $response['page'],
      'per_page' => $response['per_page'],
      'total' => $response['total'],
      'total_pages' => $response['total_pages'],
    ];
  }

  public function createUser(UserDTO $userDTO): UserDTO
  {
    $response = $this->request('POST', "/api/users", [
      'json' => $userDTO->toArray()
    ]);

    return UserDTO::fromArray($response);
  }

  private function request(string $method, string $uri, array $options = []): array
  {
    try {
      $response = $this->client->request($method, $uri, $options);

      return json_decode($response->getBody()->getContents(), true);
    } catch (ConnectException $e) {
      // Specific handling for 5xx errors
      throw new ServiceNotAvailableException("Service is down: " . $e->getMessage(), $e->getCode(), $e);
    } catch (ClientException $e) {
      // Specific handling for 4xx errors
      $statusCode = $e->getResponse()->getStatusCode();
      $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
      $errorMessage = $errorBody['error'] ?? 'An error occurred';

      switch ($statusCode) {
        case 400:
          $message = "Bad Request: $errorMessage";
          break;
        case 401:
          $message = "Unauthorized: $errorMessage";
          break;
        case 403:
          $message = "Forbidden: $errorMessage";
          break;
        case 404:
          $message = "Not Found: $errorMessage";
          break;
        case 422:
          $message = "Unprocessable Entity: $errorMessage";
          break;
        default:
          $message = "An unexpected client error occurred ($statusCode): $errorMessage";
          break;
      }

      throw new ServiceValidationException($message, $statusCode, $e);
    }
  }
}
