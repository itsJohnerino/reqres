<?php

namespace John\Reqres\DTOs;

use DateTime;
use JsonSerializable;

class UserDTO implements JsonSerializable
{
  public ?DateTime $created_at = null;

  public function __construct(
    public string $first_name,
    public string $last_name,
    public ?string $email = null,
    public ?int $id = null,
    public ?string $job = null,
    public ?string $avatar = null,
    ?string $created_at = null
  ) {
    // Convert the created_at string to a DateTime object
    $this->created_at = $created_at ? new DateTime($created_at) : null;
  }

  public static function fromArray(array $data): self
  {
    return new self(
      id: $data['id'] ?? null,
      email: $data['email'] ?? null,
      first_name: $data['first_name'],
      last_name: $data['last_name'],
      avatar: $data['avatar'] ?? null,
      created_at: $data['created_at'] ?? $data['createdAt'] ?? null,
      job: $data['job'] ?? null
    );
  }

  public function toArray()
  {
    $created_at = $this->created_at ? $this->created_at->format(DateTime::ATOM) : null;

    return [
      'id' => $this->id,
      'email' => $this->email,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'avatar' => $this->avatar,
      'job' => $this->job,
      'created_at' => $created_at,
    ];
  }

  public function jsonSerialize(): array
  {
    return $this->toArray();
  }
}
