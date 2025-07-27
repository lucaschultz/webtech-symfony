<?php

namespace App\Constant;

enum TaskFilterType: string {
  case Assigned = "ASSIGNED";
  case Created = "CREATED";

  public static function getValues(): array {
    return [self::Assigned->value, self::Created->value];
  }

  public static function fromStringOrDefault(
    string|null $value,
    ?self $default
  ): self {
    if ($value !== null && in_array($value, self::getValues(), true)) {
      return self::from($value);
    }

    return $default;
  }
}
