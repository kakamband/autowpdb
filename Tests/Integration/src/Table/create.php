<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBWorker\Worker\WorkerIntegration as Worker;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\src\Table\TestCase;

/**
 * Tests for Table->create().
 *
 * @covers Table::create
 * @group  Table
 */
class Test_Create extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnTrue() {

		$table  = new Table( new CustomTable( new Worker() ) );
		$result = $table->create(
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	public function testShouldReturnFalseWhenDBError() {
		$error = "Error while creating the DB table {$this->table_name}: A table must have at least 1 column";

		$table_definition = new CustomTable( new Worker() );
		$table_definition->set_table_schema( 'PRIMARY KEY  (file_id)' );

		$table  = new Table( $table_definition );
		$result = $table->create(
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 1, $this->get_logs() );

		foreach ( $this->get_logs() as $log ) {
			$this->assertStringStartsWith( $error, $log );
		}
	}

	public function testShouldFailWithoutLogging() {
		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );

		$table_definition = new CustomTable( new Worker() );
		$table_definition->set_table_schema( 'PRIMARY KEY  (file_id)' );

		$table  = new Table( $table_definition );
		$result = $table->create(
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->get_logs() );
	}
}
