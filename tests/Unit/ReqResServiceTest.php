<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use John\Reqres\DTOs\UserDTO;
use GuzzleHttp\Handler\MockHandler;
use John\Reqres\Services\ReqResService;
use GuzzleHttp\Exception\ConnectException;

test('a single user can be retrieved by ID', function () {
  $service = new ReqResService();
  $user = $service->getUserById(1);

  expect($user)->toBeInstanceOf(UserDTO::class);
  expect($user->id)->toBe(1);
  expect($user->id)->toBeInt();
  expect($user->email)->toBeString();
  expect($user->first_name)->toBeString();
  expect($user->last_name)->toBeString();
  expect($user->avatar)->toBeString();
});

test('a paginated list of users can be retrieved', function () {
  $service = new ReqResService();
  $paginatedUsers = $service->getPaginatedUsers(2);

  expect($paginatedUsers['users'])->toBeArray();
  expect($paginatedUsers['users'])->toContainOnlyInstancesOf(UserDTO::class);

  expect($paginatedUsers['page'])->toBeInt();
  expect($paginatedUsers['page'])->toEqual(2);

  expect($paginatedUsers['per_page'])->toBeInt();
  expect($paginatedUsers['total'])->toBeInt();
  expect($paginatedUsers['total_pages'])->toBeInt();
});

test('a new user can be created', function () {
  $newUser = UserDTO::fromArray([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'job' => 'Software Engineer',
  ]);

  $service = new ReqResService();
  $user = $service->createUser($newUser);

  expect($user)->toBeInstanceOf(UserDTO::class);
  expect($user->id)->toBeInt();
  expect($user->first_name)->toBe('John');
  expect($user->last_name)->toBe('Doe');
  expect($user->job)->toBe('Software Engineer');
  expect($user->created_at)->toBeInstanceOf(DateTime::class);
});

test('service throws ServiceNotAvailableException on network failure', function () {
  $mock = new MockHandler([
    new ConnectException("Error Communicating with Server", new Request('GET', 'test')),
  ]);

  $handlerStack = HandlerStack::create($mock);
  $client = new Client(['handler' => $handlerStack]);

  $service = new ReqResService('https://reqres.in', $client);

  // This should throw ServiceNotAvailableException due to the simulated network failure
  $service->getUserById(1);
})->throws(\John\Reqres\Exceptions\ServiceNotAvailableException::class);

test('service throws ServiceValidationException on client error', function () {
  $mock = new MockHandler([
    new Response(400, [], 'Not Found'),
  ]);

  $handlerStack = HandlerStack::create($mock);
  $client = new Client(['handler' => $handlerStack]);

  $service = new ReqResService('https://reqres.in', $client);

  // This should throw ServiceValidationException due to the simulated 400 response
  $service->getUserById(2);
})->throws(\John\Reqres\Exceptions\ServiceValidationException::class);
