<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[
  AsCommand(
    name: "app:fix-sequences",
    description: "Fix PostgreSQL sequences for auto-increment columns"
  )
]
class FixSequencesCommand extends Command {
  public function __construct(private Connection $connection) {
    parent::__construct();
  }

  protected function execute(
    InputInterface $input,
    OutputInterface $output
  ): int {
    $tables = ["task", "team", "user"]; // Add all your tables with auto-increment IDs

    foreach ($tables as $table) {
      $sequenceName = $table . "_id_seq";
      $sql = "SELECT setval('{$sequenceName}', (SELECT COALESCE(MAX(id), 1) FROM {$table}))";

      try {
        $this->connection->executeStatement($sql);
        $output->writeln("Fixed sequence for table: {$table}");
      } catch (\Exception $e) {
        $output->writeln(
          "Error fixing sequence for table {$table}: " . $e->getMessage()
        );
      }
    }

    $output->writeln("Sequences fixed successfully!");
    return Command::SUCCESS;
  }
}
