<?php
/**
 * Abstract class to define a table.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB\TableDefinition;

use JsonSerializable;
use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\DBWorker\WorkerInterface;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract class that defines the DB table.
 *
 * @since 0.1
 * @uses  $GLOBALS['wpdb']
 * @uses  wp_json_encode()
 */
abstract class AbstractTableDefinition implements TableDefinitionInterface, JsonSerializable {

	/**
	 * The (prefixed) table name.
	 *
	 * @var   string|null
	 * @since 0.1
	 */
	protected $full_table_name;

	/**
	 * Instance of the class to use to perform the operations.
	 * Default is Worker.
	 *
	 * @var   WorkerInterface
	 * @since 0.3
	 */
	protected $table_worker;

	/**
	 * Get things started.
	 *
	 * @since 0.3
	 *
	 * @param WorkerInterface $table_worker Instance of the class to use to perform the operations. Default is Worker.
	 */
	public function __construct( $table_worker = null ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		if ( ! empty( $table_worker ) && $table_worker instanceof WorkerInterface ) {
			$this->table_worker = $table_worker;
		} else {
			$this->table_worker = new Worker();
		}
	}

	/**
	 * Get the table name.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function get_table_name(): string {
		global $wpdb;

		if ( ! empty( $this->full_table_name ) ) {
			return $this->full_table_name;
		}

		$prefix = $this->is_table_global() ? $wpdb->base_prefix : $wpdb->prefix;

		$this->full_table_name = $prefix . $this->table_worker->sanitize_table_name( $this->get_table_short_name() );

		return $this->full_table_name;
	}

	/**
	 * Get the instance of the class used to perform the operations.
	 *
	 * @since 0.3
	 *
	 * @return WorkerInterface
	 */
	public function get_table_worker(): WorkerInterface {
		return $this->table_worker;
	}

	/**
	 * Convert the current object to an array.
	 *
	 * @since 0.2
	 *
	 * @return array<mixed> Array representation of the current object.
	 */
	public function jsonSerialize(): array {
		return [
			'table_version'       => $this->get_table_version(),
			'table_short_name'    => $this->get_table_short_name(),
			'table_name'          => $this->get_table_name(),
			'table_is_global'     => $this->is_table_global(),
			'primary_key'         => $this->get_primary_key(),
			'column_placeholders' => $this->get_column_placeholders(),
			'column_defaults'     => $this->get_column_defaults(),
			'table_schema'        => $this->get_table_schema(),
		];
	}

	/**
	 * Convert the current object to a string.
	 *
	 * @since 0.2
	 *
	 * @return string String representation of the current object. An empty string on error.
	 */
	public function __toString(): string {
		$string = wp_json_encode( $this );

		if ( false === $string ) {
			return '';
		}

		return $string;
	}
}
