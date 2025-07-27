<?php

namespace App\Constant;

enum TaskStatus: string {
  case Todo = "TODO";
  case InProgress = "IN_PROGRESS";
  case Done = "DONE";
}
