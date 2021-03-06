<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->clone_table().
 *
 * @covers Worker::clone_table
 * @group  Worker
 */
class Test_CloneTable extends TestCase {
	protected $drop_table        = true;
	protected $drop_target_table = true;

	public function testShouldCloneTable() {
		global $wpdb;

		// Create table and contents.
		$this->create_table();
		$this->add_row( 'foobar' );
		$this->add_row( 'barbaz' );

		$result = ( new Worker() )->clone_table( $this->table_name, $this->target_table_name );

		// Check that the result is true.
		$this->assertTrue( $result );

		$row = $this->get_last_row( $this->target_table_name );

		// Check that the target table exists.
		$error = "Table '{$wpdb->dbname}.{$this->target_table_name}' doesn't exist";

		$this->assertNotSame( $error, $wpdb->last_error );

		// Check that the target table is empty.
		$this->assertNull( $row );
	}

	public function testShouldNotCloneTableWhenItDoesNotExist() {
		global $wpdb;

		$result = ( new Worker() )->clone_table( $this->table_name, $this->target_table_name );

		// Check that the result is false.
		$this->assertFalse( $result );
	}
}
