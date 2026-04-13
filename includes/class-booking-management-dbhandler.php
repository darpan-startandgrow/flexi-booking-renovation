<?php
/**
 * Database handler for the booking plugin.
 *
 * Provides a centralized abstraction over $wpdb for all CRUD operations.
 * All database access within the plugin MUST go through this class.
 *
 * @since      1.0.0
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */
class BM_DBhandler {


	/**
	 * Cached instance of the activator for table name lookups.
	 *
	 * @var Booking_Management_Activator|null
	 */
	private $activator_instance = null;

	/**
	 * Get the activator instance (cached to avoid repeated instantiation).
	 *
	 * @return Booking_Management_Activator
	 */
	private function get_activator() {
		if ( $this->activator_instance === null ) {
			$this->activator_instance = new Booking_Management_Activator();
		}
		return $this->activator_instance;
	}


	/**
	 * Insert a row into the specified table.
	 *
	 * @param string     $identifier Table identifier (e.g., 'SERVICE', 'BOOKING').
	 * @param array      $data       Column => value pairs.
	 * @param array|null $format     Optional format array for wpdb.
	 * @return int|false Insert ID on success, false on failure.
	 */
	public function insert_row( $identifier, $data, $format = null ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );
		$result       = $wpdb->insert( $table, $data, $format );
		if ( $result !== false ) {
			return $wpdb->insert_id;
		}
		return false;
	}//end insert_row()


	public function update_row( $identifier, $unique_field, $unique_field_value, $data, $format = null, $where_format = null ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );
		if ( $unique_field === false ) {
			$unique_field = $bm_activator->get_db_table_unique_field_name( $identifier );
		}

		if ( is_numeric( $unique_field_value ) ) {
			$unique_field_value = (int) $unique_field_value;
			$query              = $wpdb->prepare( "SELECT * from $table where $unique_field = %d", $unique_field_value );
		} else {
			$query = $wpdb->prepare( "SELECT * from $table where $unique_field = %s", $unique_field_value );
		}

		if ( $query != null ) {
			$result = $wpdb->get_row( $query );
		}

		if ( $result === null ) {
			return false;
		}

		$where = array( $unique_field => $unique_field_value );
		return $wpdb->update( $table, $data, $where, $format, $where_format );
	}//end update_row()


	public function remove_row( $identifier, $unique_field, $unique_field_value, $where_format = null ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );
		if ( $unique_field === false ) {
			$unique_field = $bm_activator->get_db_table_unique_field_name( $identifier );
		}

		if ( is_numeric( $unique_field_value ) ) {
			$unique_field_value = (int) $unique_field_value;
			$query              = $wpdb->prepare( "SELECT * from $table WHERE $unique_field = %d", $unique_field_value );
		} else {
			$query = $wpdb->prepare( "SELECT * from $table WHERE $unique_field = %s", $unique_field_value );
		}

		if ( $query != null ) {
			$result = $wpdb->get_row( $query );
		}

		if ( $result === null ) {
			return false;
		}

		$where = array( $unique_field => $unique_field_value );
		return $wpdb->delete( $table, $where, $where_format );
	}//end remove_row()


	public function get_row( $identifier, $unique_field_value, $unique_field = false, $output_type = 'OBJECT' ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );
		$result       = null;
		if ( $unique_field === false ) {
			$unique_field = $bm_activator->get_db_table_unique_field_name( $identifier );
		}

		if ( is_numeric( $unique_field_value ) ) {
			$unique_field_value = (int) $unique_field_value;
			$query              = $wpdb->prepare( "SELECT * from $table where $unique_field = %d", $unique_field_value );
		} else {
			$query = $wpdb->prepare( "SELECT * from $table where $unique_field = %s", $unique_field_value );
		}

		if ( $query != null ) {
			$result = $wpdb->get_row( $query, $output_type );
		}

		if ( $result != null ) {
			return $result;
		}
	}//end get_row()


	public function get_value( $identifier, $field, $unique_field_value, $unique_field = false ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		if ( $unique_field === false ) {
			$unique_field = $bm_activator->get_db_table_unique_field_name( $identifier );
		}

		if ( is_numeric( $unique_field_value ) ) {
			$unique_field_value = (int) $unique_field_value;
			$query              = $wpdb->prepare( "SELECT $field from $table where $unique_field = %d", $unique_field_value );
		} else {
			$query = $wpdb->prepare( "SELECT $field from $table where $unique_field = %s", $unique_field_value );
		}

		if ( $query != null ) {
			$result = $wpdb->get_var( $query );
		}

		if ( isset( $result ) && $result != null ) {
			return $result;
		}
	}//end get_value()


	public function get_value_with_multicondition( $identifier, $field, $where ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );
		$qry          = "SELECT $field from $table where";
		$i            = 0;
		$args         = array();
		foreach ( $where as $column_name => $column_value ) {
			if ( $i !== 0 ) {
				$qry .= ' AND';
			}

			$format = $bm_activator->get_db_table_field_type( $identifier, $column_name );
			$qry   .= " $column_name = $format";

			if ( is_numeric( $column_value ) ) {
				$args[] = (int) $column_value;
			} else {
				$args[] = $column_value;
			}

			++$i;
		}

		$results = $wpdb->get_var( $wpdb->prepare( $qry, $args ) );
		return $results;
	}//end get_value_with_multicondition()


	public function get_all_result( $identifier, $column = '*', $where = 1, $result_type = 'results', $offset = 0, $limit = false, $sort_by = null, $descending = false, $additional = '', $output = 'OBJECT', $distinct = false ) {
		global $wpdb;
		$bm_activator   = new Booking_Management_Activator();
		$table          = $bm_activator->get_db_table_name( $identifier );
		$unique_id_name = $bm_activator->get_db_table_unique_field_name( $identifier );
		$args           = array();
		if ( ! $sort_by ) {
			$sort_by = $unique_id_name;
		}

		if ( is_string( $column ) && strpos( $column, 'distinct' ) ) {
			$column   = str_replace( 'distinct ', '', $column );
			$distinct = true;
		} elseif ( is_string( $column ) && strpos( $column, 'DISTINCT' ) ) {
			$column   = str_replace( 'DISTINCT ', '', $column );
			$distinct = true;
		}

		if ( $column != '' && ! is_array( $column ) && $distinct == false ) {
			$qry = "SELECT $column FROM $table WHERE";
		} elseif ( $column != '' && ! is_array( $column ) && $distinct == true ) {
			$qry = "SELECT DISTINCT $column FROM $table WHERE";
		} elseif ( is_array( $column ) ) {
			$qry = 'SELECT ' . implode( ', ', $column ) . " FROM $table WHERE";
		}

		if ( is_array( $where ) ) {
			$i = 0;
			foreach ( $where as $column_name => $column_value ) {
				if ( $i !== 0 ) {
					$qry .= ' AND';
				}

				$format = $bm_activator->get_db_table_field_type( $identifier, $column_name );
				$qry   .= " $column_name = $format";

				if ( is_numeric( $column_value ) ) {
					$args[] = (int) $column_value;
				} else {
					$args[] = $column_value;
				}

				++$i;
			}

			if ( $additional != '' ) {
				$qry .= ' ' . $additional;
			}
		} elseif ( $where == 1 ) {
			if ( $additional != '' ) {
				$qry .= ' ' . $additional;
			} else {
				$qry .= ' 1';
			}
		} //end if

		if ( $descending === false ) {
			$qry .= " ORDER BY $sort_by";
		} else {
			$qry .= " ORDER BY $sort_by DESC";
		}

		if ( $limit === false ) {
			$qry .= '';
		} else {
			$qry .= $wpdb->prepare( ' LIMIT %d OFFSET %d', intval( $limit ), intval( $offset ) );
		}

		if ( $result_type === 'results' || $result_type === 'row' || $result_type === 'var' ) {
			$method_name = 'get_' . $result_type;
			if ( count( $args ) === 0 ) {
				if ( $result_type === 'results' ) :
					$results = $wpdb->$method_name( $qry, $output );
				else :
					$results = $wpdb->$method_name( $qry );
				endif;
			} elseif ( $result_type === 'results' ) {
					$results = $wpdb->$method_name( $wpdb->prepare( $qry, $args ), $output );
			} else {
				$results = $wpdb->$method_name( $wpdb->prepare( $qry, $args ) );
			}
		} else {
			return null;
		}
		
		if ( is_array( $results ) && count( $results ) === 0 ) {
			return null;
		}

		return $results;
	}//end get_all_result()


	/**
	 * get results with join
	 *
	 * @author Darpan
	 */
	public function get_results_with_join( $tables, $columns = '*', $joins = array(), $where = array(), $result_type = 'results', $offset = 0, $limit = false, $sort_by = null, $descending = false, $additional = '', $increase_group_concat_length = false, $group_concat_length = 10000, $output = 'OBJECT' ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$base_table   = $bm_activator->get_db_table_name( $tables[0] );
		$base_alias   = isset( $tables[1] ) ? $tables[1] : 's';

		if ( $increase_group_concat_length ) {
			$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len = %d;', intval( $group_concat_length ) ) );
		}

		$qry = "SELECT $columns FROM $base_table $base_alias";

		foreach ( $joins as $join ) {
			$join_type      = isset( $join['type'] ) ? strtoupper( $join['type'] ) : 'INNER';
			$join_table     = $bm_activator->get_db_table_name( $join['table'] );
			$join_alias     = isset( $join['alias'] ) ? $join['alias'] : 'c';
			$join_condition = $join['on'];

			$qry .= " $join_type JOIN $join_table $join_alias ON $join_condition";
		}

		$args = array();

		if ( ! empty( $where ) ) {
			$where_clauses = array();

			foreach ( $where as $field => $condition ) {
				// Check if condition is an array with multiple parts
				if ( is_array( $condition ) ) {
					// Handle conditions like b.booking_date >= '2024-01-01' AND b.booking_date <= '2024-12-31'
					$condition_clauses = array();
					foreach ( $condition as $operator => $value ) {
						// Ensure each condition is added properly
						$format = $bm_activator->get_db_table_field_type( $tables[0], str_replace( "$base_alias.", '', $field ) );

						if ( $operator === 'IN' || $operator === 'NOT IN' ) {
							if ( is_array( $value ) && ! empty( $value ) ) {
								$placeholders        = array_map(
									function ( $val ) {
										return is_int( $val ) ? '%d' : '%s';
									},
									$value
								);
								$placeholder_string  = implode( ', ', $placeholders );
								$condition_clauses[] = "$field $operator ($placeholder_string)";
								$args                = array_merge( $args, $value );
							}
						} elseif ( $operator === 'LIKE' ) {
							$condition_clauses[] = "$field LIKE $format";
							$args[]              = $value;
						} elseif ( in_array( $operator, array( '=', '!=', '<', '>', '>=', '<=' ) ) ) {
							$condition_clauses[] = "$field $operator $format";
							$args[]              = $value;
						}
					}
					// Join multiple conditions with AND
					if ( count( $condition_clauses ) > 0 ) {
						$where_clauses[] = '(' . implode( ' AND ', $condition_clauses ) . ')';
					}
				} else {
					// Handle single condition (e.g., b.booking_date >= '2024-01-01')
					list($operator, $value) = $condition;

					$format = $bm_activator->get_db_table_field_type( $tables[0], str_replace( "$base_alias.", '', $field ) );

					if ( $operator === 'IN' || $operator === 'NOT IN' ) {
						if ( is_array( $value ) && ! empty( $value ) ) {
							$placeholders       = array_map(
								function ( $val ) {
									return is_int( $val ) ? '%d' : '%s';
								},
								$value
							);
							$placeholder_string = implode( ', ', $placeholders );
							$where_clauses[]    = "$field $operator ($placeholder_string)";
							$args               = array_merge( $args, $value );
						}
					} elseif ( $operator === 'LIKE' ) {
						$where_clauses[] = "$field LIKE $format";
						$args[]          = $value;
					} elseif ( in_array( $operator, array( '=', '!=', '<', '>', '>=', '<=' ) ) ) {
						$where_clauses[] = "$field $operator $format";
						$args[]          = $value;
					}
				}
			}

			if ( count( $where_clauses ) > 0 ) {
				$qry .= ' WHERE ' . implode( ' AND ', $where_clauses );
			}
		}

		if ( $additional != '' ) {
			$qry .= ' ' . $additional;
		}

		if ( $sort_by ) {
			$qry .= " ORDER BY $sort_by";
			if ( $descending ) {
				$qry .= ' DESC';
			}
		}

		if ( $limit ) {
			$qry .= $wpdb->prepare( ' LIMIT %d OFFSET %d', intval( $limit ), intval( $offset ) );
		}

		$method_name = 'get_' . $result_type;
		if ( count( $args ) === 0 ) {
			if ( $result_type === 'results' ) :
				$results = $wpdb->$method_name( $qry, $output );
			else :
				$results = $wpdb->$method_name( $qry );
			endif;
		} elseif ( $result_type === 'results' ) {
				$results = $wpdb->$method_name( $wpdb->prepare( $qry, $args ), $output );
		} else {
			$results = $wpdb->$method_name( $wpdb->prepare( $qry, $args ) );
		}

		if ( is_array( $results ) && count( $results ) === 0 ) {
			return null;
		}

		return $results;
	}

	public function bm_count( $identifier, $where = 1, $data_specifiers = '' ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table_name   = $bm_activator->get_db_table_name( $identifier );
		if ( $data_specifiers == '' ) {
			$unique_id_name = $bm_activator->get_db_table_unique_field_name( $identifier );
			if ( $unique_id_name === false ) {
				return false;
			}
		} else {
			$unique_id_name = $data_specifiers;
		}

		$qry = "SELECT COUNT($unique_id_name) FROM $table_name WHERE ";

		if ( is_array( $where ) ) {
			$i = 0;
			foreach ( $where as $column_name => $column_value ) {
				if ( $i != 0 ) {
					$qry .= 'AND ';
				}

				if ( is_numeric( $column_value ) ) {
					$column_value = (int) $column_value;
					$qry         .= $wpdb->prepare( "$column_name = %d ", $column_value );
				} else {
					$qry .= $wpdb->prepare( "$column_name = %s ", $column_value );
				}
			}
		} elseif ( $where == 1 ) {
			$qry .= '1 ';
		}

		$count = $wpdb->get_var( $qry );

		if ( $count === null ) {
			return false;
		}

		return (int) $count;
	}//end bm_count()


	public function get_global_option_value( $option, $default = '' ) {
		$value = get_option( $option, $default );
		if ( ! isset( $value ) || $value == '' ) {
			$value = $default;
		}

		$value = maybe_unserialize( $value );
		return $value;
	}//end get_global_option_value()


	public function update_global_option_value( $option, $value ) {
		if ( is_array( $value ) ) {
			maybe_serialize( $value );
		}

		update_option( $option, $value );
	}//end update_global_option_value()


	public function delete_global_option_value( $option ) {
		delete_option( $option );
	}//end delete_global_option_value()


	public function bm_get_pagination( $num_of_pages, $pagenum, $base, $type = 'plain' ) {
		$args = array(
			'base'               => esc_url_raw( add_query_arg( 'pagenum', '%#%', $base ) ),
			'format'             => '',
			'total'              => $num_of_pages,
			'current'            => $pagenum,
			'show_all'           => false,
			'end_size'           => 1,
			'mid_size'           => 2,
			'prev_next'          => true,
			'prev_text'          => __( '&laquo;', 'service-booking' ),
			'next_text'          => __( '&raquo;', 'service-booking' ),
			'type'               => $type == 'list' ? 'list' : 'plain',
			'add_args'           => false,
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => '',
		);

		$page_links = paginate_links( $args );
		return $page_links;
	}//end bm_get_pagination()


	// functions by Darpan

	/**
	 * get columns
	 *
	 * @author Darpan
	 */
	public function get_table_columns( $identifier, $exclude_columns = array() ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		$columns = $wpdb->get_col(
			$wpdb->prepare(
				'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s',
				$table
			)
		);

		// Exclude specified columns
		if ( ! empty( $exclude_columns ) ) {
			$columns = array_diff( $columns, $exclude_columns );
		}

		return $columns;
	}//end get_table_columns()


	/**
	 * Get results filtered by specific columns.
	 *
	 * @author Darpan
	 *
	 * @param string $identifier      Table identifier.
	 * @param array  $columns         Columns to select.
	 * @param array  $exclude_columns Columns to exclude.
	 * @param string $result_type     Result type: 'results', 'row', or 'var'.
	 * @param string $output          Output type for results.
	 * @return mixed Query results or null on failure.
	 */
	public function get_results_by_columns( $identifier, $columns, $exclude_columns = array(), $result_type = 'results', $output = 'OBJECT' ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		// Exclude specified columns
		if ( ! empty( $exclude_columns ) ) {
			$columns = array_diff( $columns, $exclude_columns );
		}

		if ( empty( $columns ) ) {
			return null;
		}

		$column_list = implode( ', ', array_map( 'esc_sql', $columns ) );
		$safe_table  = esc_sql( $table );
		$query       = "SELECT {$column_list} FROM {$safe_table}";

		$method_name = 'get_' . $result_type;
		if ( ! in_array( $result_type, array( 'results', 'row', 'var' ), true ) ) {
			return null;
		}

		if ( $result_type === 'results' ) {
			$results = $wpdb->$method_name( $query, $output );
		} else {
			$results = $wpdb->$method_name( $query );
		}

		return $results;
	}//end get_results_by_columns()


	/**
	 * get results by column
	 *
	 * @author Darpan
	 */
	public function filter_existing_data_by_columns( $data, $columns, $exclude_columns = array(), $column_ordered = false, $indexed = false ) {
		// Exclude specified columns
		if ( ! empty( $exclude_columns ) ) {
			$columns = array_diff( $columns, $exclude_columns );
		}

		// Convert data to array format if it's an object
		/**$data = array_map( 'object_to_array', $data );*/

		// Filter data based on selected columns
		$filtered_data = array_map(
			function ( $row ) use ( $columns ) {
				return array_filter(
					$row,
					function ( $key ) use ( $columns ) {
						return in_array( $key, $columns );
					},
					ARRAY_FILTER_USE_KEY
				);
			},
			$data
		);

		if ( $column_ordered ) {
			$filtered_data = array_map(
				function ( $v ) use ( $columns ) {
					return array_replace( array_flip( $columns ), $v );
				},
				$filtered_data
			);
		}

		// filtered data to an indexed array format
		if ( $indexed ) {
			$filtered_data = array_map( 'array_values', $filtered_data );
		}

		return $filtered_data;
	} //end get_results_by_columns()


	/**
	 * Helper function to convert objects to arrays recursively.
	 *
	 * @param mixed $obj The object or value to convert.
	 * @return mixed Converted array or the original value.
	 */
	public function object_to_array( $obj ) {
		if ( is_object( $obj ) ) {
			$obj = (array) $obj;
		}
		if ( is_array( $obj ) ) {
			$new = array();
			foreach ( $obj as $key => $val ) {
				$new[ $key ] = $this->object_to_array( $val );
			}
		} else {
			$new = $obj;
		}
		return $new;
	}


	/**
	 * Apply sql conditions to an existing data set
	 *
	 * @author Darpan
	 */
	public function bm_apply_sql_conditions( $data, $conditions ) {
		$filteredData = array();

		foreach ( $data as $row ) {
			$conditionSatisfied = $this->bm_evaluate_condition( $row, $conditions );

			if ( $conditionSatisfied ) {
				$filteredData[] = $row;
			}
		}

		return $filteredData;
	}//end bm_apply_sql_conditions()


	/**
	 * Evaluate conditions to be applied to an existing data set
	 *
	 * @author Darpan
	 */
	public function bm_evaluate_condition( $row, $conditions ) {
		$conditionSatisfied = true;

		foreach ( $conditions as $conditionKey => $conditionValue ) {
			if ( is_array( $conditionValue ) ) {
				// Handle sub-conditions (nested conditions)
				$subConditionSatisfied = $this->bm_evaluate_condition( $row, $conditionValue );

				if ( $conditionKey === 'or' && ! $subConditionSatisfied ) {
					$conditionSatisfied = false;
					break;
				} elseif ( $conditionKey === 'and' && ! $subConditionSatisfied ) {
					$conditionSatisfied = false;
				}
			} elseif ( strpos( $conditionKey, 'in' ) !== false ) {
				// Handle 'IN' condition
				$field           = str_replace( ' in', '', $conditionKey );
				$values          = explode( ',', str_replace( array( '(', ')' ), '', $conditionValue ) );
				$columnSelection = is_object( $row ) ? $row->$field : $row[ $field ];

				if ( ! in_array( $columnSelection, $values ) ) {
					$conditionSatisfied = false;
					break;
				}
			} elseif ( strpos( $conditionKey, 'not in' ) !== false ) {
				// Handle 'NOT IN' condition
				$field           = str_replace( ' not in', '', $conditionKey );
				$values          = explode( ',', str_replace( array( '(', ')' ), '', $conditionValue ) );
				$columnSelection = is_object( $row ) ? $row->$field : $row[ $field ];

				if ( in_array( $columnSelection, $values ) ) {
					$conditionSatisfied = false;
					break;
				}
			} else {
				// Handle comparison conditions
				$field           = $conditionKey;
				$operator        = '=';
				$value           = $conditionValue;
				$columnSelection = is_object( $row ) ? $row->$field : $row[ $field ];

				if ( strpos( $conditionKey, ' ' ) !== false ) {
					list($field, $operator) = explode( ' ', $conditionKey, 2 );
				}

				switch ( $operator ) {
					case '=':
						if ( $columnSelection != $value ) {
							$conditionSatisfied = false;
						}
						break;
					case '>':
						if ( $columnSelection <= $value ) {
							$conditionSatisfied = false;
						}
						break;
					case '<':
						if ( $columnSelection >= $value ) {
							$conditionSatisfied = false;
						}
						break;
						// Add more comparison operators as needed
					default:
						// Unsupported operator
						$conditionSatisfied = false;
						break;
				} //end switch

				if ( ! $conditionSatisfied ) {
					break;
				}
			} //end if
		} //end foreach

		return $conditionSatisfied;
	}//end bm_evaluate_condition()


	/**
	 * Apply offset and limit to an exisiting data set and sort
	 *
	 * @author Darpan
	 */
	public function bm_apply_offset_limit_and_sort_existing_data( $data, $offset, $limit, $sort = false, $column = '', $order = 'ASC' ) {
		$offset = intval( $offset );
		$limit  = intval( $limit );

		if ( $offset < 0 || $limit < 0 ) {
			return $data;
		}

		if ( $sort == true && ! empty( $column ) && ! empty( $data ) ) {
			$sortOrder    = ( $order === 'DESC' ) ? SORT_DESC : SORT_ASC;
			$columnValues = array_column( $data, $column );
			array_multisort( $columnValues, $sortOrder, $data );
		}

		if ( ! empty( $data ) ) {
			if ( ! empty( $limit ) ) {
				$data = array_slice( $data, $offset, $limit );
			} else {
				$data = array_slice( $data, $offset );
			}
		}

		return $data;
	}//end bm_apply_offset_limit_and_sort_existing_data()


	/**
	 * Group by exisiting data
	 *
	 * @author Darpan
	 */
	public function bm_group_data_by_column( $data, $columns ) {
		$grouped_data = array();

		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $item ) {
				if ( is_array( $item ) ) {
					$group_key = '';
					foreach ( $columns as $column ) {
						if ( isset( $item[ $column ] ) ) {
							$group_key .= $item[ $column ] . '_';
						} else {
							// If the column does not exist in the item, skip this item
							continue 2;
						}
					}
					// Remove the trailing underscore
					$group_key = rtrim( $group_key, '_' );

					if ( ! isset( $grouped_data[ $group_key ] ) ) {
						$grouped_data[ $group_key ] = array();
					}
					$grouped_data[ $group_key ][] = $item;
				} else {
					$group_key = '';
					foreach ( $columns as $column ) {
						if ( isset( $item->$column ) ) {
							$group_key .= $item->$column . '_';
						} else {
							// If the column does not exist in the item, skip this item
							continue 2;
						}
					}
					// Remove the trailing underscore
					$group_key = rtrim( $group_key, '_' );

					if ( ! isset( $grouped_data[ $group_key ] ) ) {
						$grouped_data[ $group_key ] = array();
					}
					$grouped_data[ $group_key ] = $item;
				}
			}
		}

		return $grouped_data;
	}//end bm_group_data_by_column()


	/**
	 * Get a page by title
	 *
	 * @author Darpan
	 */
	public function bm_fetch_page_by_title( $page_title, $output = OBJECT, $post_type = 'page', $return_type = '' ) {

		global $sitepress;
		if ( $sitepress ) {
			$default_lang = $sitepress->get_default_language();
			$current_lang = $sitepress->get_current_language();
			$sitepress->switch_lang( $default_lang, true );
		}
		$page  = null;
		$args  = array(
			'title'                  => $page_title,
			'post_type'              => $post_type,
			'post_status'            => get_post_stati(),
			'posts_per_page'         => 1,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'no_found_rows'          => true,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
		);
		$query = new WP_Query( $args );
		$pages = $query->posts;

		if ( $sitepress ) {
			$sitepress->switch_lang( $current_lang, true );
			if ( empty( $pages ) ) {
				return null;
			}
			$original_page = $pages[0];

			$translated_id = apply_filters( 'wpml_object_id', $original_page->ID, $post_type, true, $current_lang );

			if ( ! $translated_id ) {
				return null;
			}
			return get_permalink( $original_page->ID );

		}

		if ( ! empty( $pages ) ) {
			$page = get_post( $pages[0], $output );
		}

		if ( $return_type == 'url' ) {
			return get_permalink( $page->ID );
		}

		return $page;
	}//end bm_fetch_page_by_title()


	/**
	 * Get meta option value
	 *
	 * @author Darpan
	 */
	public function get_meta_option_value( $postId, $option ) {
		$value = get_post_meta( $postId, $option, true );
		$value = maybe_unserialize( $value );
		return $value;
	}//end get_meta_option_value()


	/**
	 * Update meta option value
	 *
	 * @author Darpan
	 */
	public function update_meta_option_value( $postId, $option, $value ) {
		if ( is_array( $value ) ) {
			maybe_serialize( $value );
		}

		update_post_meta( $postId, $option, $value );
	}//end update_meta_option_value()


	public function get_global_options( $options = array(), $defaults = array() ) {
		if ( empty( $options ) ) {
			return array();
		}

		$default_values = array_fill_keys( $options, '' );
		if ( ! empty( $defaults ) ) {
			$default_values = array_merge( $default_values, $defaults );
		}

		$values = array();
		foreach ( $options as $option ) {
			$values[ $option ] = maybe_unserialize( get_option( $option, $default_values[ $option ] ) );
		}

		return $values;
	}


	/**
	 * Update multiple global options in a batch.
	 *
	 * @param array $options An associative array of options to be updated in the form 'option_name' => 'option_value'.
	 */
	public function update_global_option_value_batch( $options ) {
		foreach ( $options as $option_name => $option_value ) {
			update_option( $option_name, $option_value );
		}
	}


	/**
	 * Save transient data
	 *
	 * @author Darpan
	 */
	public function bm_save_data_to_transient( $transient_name, $data, $expiry = 0 ) {
		if ( is_array( $data ) ) {
			maybe_serialize( $data );
		}

		if ( ! empty( $expiry ) ) {
			$expiration = $expiry * HOUR_IN_SECONDS;
			set_transient( $transient_name, $data, $expiration );
		} else {
			set_transient( $transient_name, $data );
		}
	}//end bm_save_data_to_transient()


	/**
	 * Fetch transient data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_data_from_transient( $transient_name ) {
		$data = get_transient( $transient_name );
		$data = maybe_unserialize( $data );
		return $data;
	}//end bm_fetch_data_from_transient()


	/**
	 * Delete transient data
	 *
	 * @author Darpan
	 */
	public function bm_delete_transient( $transient_name ) {
		delete_transient( $transient_name );
	}//end bm_delete_transient()


	/**
	 * Begin a database transaction.
	 *
	 * @since 1.0.0
	 * @return bool True on success, false on failure.
	 */
	public function begin_transaction() {
		global $wpdb;
		return $wpdb->query( 'START TRANSACTION' ) !== false;
	}


	/**
	 * Commit the current database transaction.
	 *
	 * @since 1.0.0
	 * @return bool True on success, false on failure.
	 */
	public function commit_transaction() {
		global $wpdb;
		return $wpdb->query( 'COMMIT' ) !== false;
	}


	/**
	 * Roll back the current database transaction.
	 *
	 * @since 1.0.0
	 * @return bool True on success, false on failure.
	 */
	public function rollback_transaction() {
		global $wpdb;
		return $wpdb->query( 'ROLLBACK' ) !== false;
	}


	/**
	 * Execute a callback within a database transaction.
	 *
	 * Automatically commits on success or rolls back on failure/exception.
	 *
	 * @since 1.0.0
	 * @param callable $callback The operations to execute inside the transaction.
	 * @return mixed The return value of the callback, or WP_Error on failure.
	 */
	public function with_transaction( $callback ) {
		$this->begin_transaction();
		try {
			$result = call_user_func( $callback );
			$this->commit_transaction();
			return $result;
		} catch ( \Exception $e ) {
			$this->rollback_transaction();
			return new \WP_Error( 'db_transaction_failed', $e->getMessage() );
		}
	}


	// -------------------------------------------------------------------------
	// Low-level $wpdb proxy helpers
	// These keep all $wpdb usage centralized inside this class while still
	// allowing callers to build parameterized queries without importing $wpdb.
	// -------------------------------------------------------------------------

	/**
	 * Escape a string for use in a SQL LIKE clause.
	 *
	 * Wraps $wpdb->esc_like() so callers never need to import $wpdb directly.
	 *
	 * @param string $text The raw search string.
	 * @return string Escaped string safe for use in a LIKE pattern.
	 */
	public function esc_like( string $text ): string {
		global $wpdb;
		return $wpdb->esc_like( $text );
	}


	/**
	 * Prepare a parameterized SQL statement.
	 *
	 * Wraps $wpdb->prepare() so callers never need to import $wpdb directly.
	 * All arguments after $query are passed as substitution values.
	 *
	 * @param string $query SQL template with sprintf-style placeholders (%s, %d, %f).
	 * @param mixed  ...$args Values to substitute into the placeholders.
	 * @return string Prepared (safe) SQL string.
	 */
	public function prepare_sql( string $query, ...$args ): string {
		global $wpdb;
		return $wpdb->prepare( $query, ...$args );
	}


	/**
	 * Execute a raw SQL query and return all matching rows.
	 *
	 * Use only for complex queries (e.g. UNION) that cannot be expressed
	 * through the structured helper methods. The query MUST be pre-prepared
	 * via prepare_sql() before being passed here.
	 *
	 * @param string $sql    A fully-prepared SQL string (output of prepare_sql()).
	 * @param string $output Output format: OBJECT, ARRAY_A, or ARRAY_N.
	 * @return array|null Array of rows on success, null when no results.
	 */
	public function get_results_raw( string $sql, string $output = OBJECT ): ?array {
		global $wpdb;
		$results = $wpdb->get_results( $sql, $output );
		return ( is_array( $results ) && ! empty( $results ) ) ? $results : null;
	}


	/**
	 * Execute a raw SQL query and return a single scalar value.
	 *
	 * Use only when no structured method can express the query. The query
	 * MUST be pre-prepared via prepare_sql() before being passed here.
	 *
	 * @param string $sql A fully-prepared SQL string (output of prepare_sql()).
	 * @return string|null The first column of the first row, or null.
	 */
	public function get_var_raw( string $sql ): ?string {
		global $wpdb;
		return $wpdb->get_var( $sql );
	}


	/**
	 * Delete all transients whose names start with the given prefix.
	 *
	 * Removes both the transient value and its corresponding timeout entry
	 * from the options table in a single query.
	 *
	 * @param string $prefix Prefix to match (e.g. 'FLEXI').
	 * @return void
	 */
	public function delete_transients_by_prefix( string $prefix ): void {
		// Restrict prefix to safe characters only to prevent injection.
		$prefix = preg_replace( '/[^a-zA-Z0-9_-]/', '', $prefix );
		if ( empty( $prefix ) ) {
			return;
		}
		global $wpdb;
		$like_value   = $wpdb->esc_like( '_transient_' . $prefix ) . '%';
		$like_timeout = $wpdb->esc_like( '_transient_timeout_' . $prefix ) . '%';
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				$like_value,
				$like_timeout
			)
		);
	}


	/**
	 * Execute a SELECT ... FOR UPDATE on rows matching given conditions.
	 *
	 * Must be called inside an active transaction. Locks the matching rows
	 * (and the surrounding gap in InnoDB) until the transaction is committed
	 * or rolled back, serialising concurrent capacity checks for the same slot.
	 *
	 * @since 1.0.0
	 * @param string $identifier Table identifier (e.g. 'SLOTCOUNT').
	 * @param array  $where      Column => value conditions (ANDed together).
	 * @return array|null Locked rows, or null when none exist.
	 */
	public function select_for_update( string $identifier, array $where ): ?array {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		if ( ! $table || empty( $where ) ) {
			return null;
		}

		$conditions = array();
		$values     = array();
		foreach ( $where as $col => $val ) {
			// Sanitize column name to safe identifier characters only.
			$col = preg_replace( '/[^a-zA-Z0-9_]/', '', $col );
			if ( empty( $col ) ) {
				continue;
			}
			$conditions[] = is_numeric( $val ) ? "`$col` = %d" : "`$col` = %s";
			$values[]     = $val;
		}

		if ( empty( $conditions ) ) {
			return null;
		}

		$where_sql = implode( ' AND ', $conditions );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql     = $wpdb->prepare( "SELECT * FROM `$table` WHERE $where_sql FOR UPDATE", ...$values );
		$results = $wpdb->get_results( $sql );
		return ( ! empty( $results ) ) ? $results : null;
	}


	/**
	 * Check whether at least one row matching given conditions exists.
	 *
	 * @since 1.0.0
	 * @param string $identifier Table identifier.
	 * @param array  $where      Column => value conditions (ANDed together).
	 * @return bool True if at least one matching row exists.
	 */
	public function record_exists( string $identifier, array $where ): bool {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		if ( ! $table || empty( $where ) ) {
			return false;
		}

		$conditions = array();
		$values     = array();
		foreach ( $where as $col => $val ) {
			$col = preg_replace( '/[^a-zA-Z0-9_]/', '', $col );
			if ( empty( $col ) ) {
				continue;
			}
			$conditions[] = is_numeric( $val ) ? "`$col` = %d" : "`$col` = %s";
			$values[]     = $val;
		}

		if ( empty( $conditions ) ) {
			return false;
		}

		$where_sql = implode( ' AND ', $conditions );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM `$table` WHERE $where_sql LIMIT 1", ...$values );
		$count = (int) $wpdb->get_var( $sql );
		return $count > 0;
	}


	/**
	 * Insert a row only if no existing row matches the deduplication conditions.
	 *
	 * @since 1.0.0
	 * @param string $identifier Table identifier.
	 * @param array  $check      Column => value pairs used as the uniqueness check.
	 * @param array  $data       Column => value pairs to insert.
	 * @return int|false Insert ID on success, false if row already existed or insert failed.
	 */
	public function insert_if_not_exists( string $identifier, array $check, array $data ) {
		if ( $this->record_exists( $identifier, $check ) ) {
			return false;
		}

		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		if ( ! $table ) {
			return false;
		}

		$result = $wpdb->insert( $table, $data );
		if ( $result !== false ) {
			return $wpdb->insert_id;
		}
		return false;
	}


	/**
	 * Get the total pooled usage of a global extra across ALL services for a given date.
	 *
	 * Sums the `slots_booked` column from EXTRASLOTCOUNT where the extra_type is 'global'
	 * and the extra_svc_id matches the provided global_extra_id.
	 *
	 * @since 1.0.0
	 * @param int    $global_extra_id The global extra ID.
	 * @param string $date            The booking date (Y-m-d).
	 * @return int Total slots booked across all services.
	 */
	public function get_global_extra_pooled_usage( int $global_extra_id, string $date ): int {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( 'EXTRASLOTCOUNT' );

		if ( ! $table || empty( $global_extra_id ) || empty( $date ) ) {
			return 0;
		}

		$sql = $wpdb->prepare(
			"SELECT COALESCE(SUM(`slots_booked`), 0) FROM `$table` WHERE `extra_svc_id` = %d AND `extra_type` = %s AND `booking_date` = %s AND `is_active` = 1",
			$global_extra_id,
			'global',
			$date
		);

		return (int) $wpdb->get_var( $sql );
	}


	/**
	 * SELECT ... FOR UPDATE on the global_extras table for atomic capacity checks.
	 *
	 * Must be called within an active transaction.
	 *
	 * @since 1.0.0
	 * @param int $global_extra_id The global extra ID to lock.
	 * @return object|null The locked row or null.
	 */
	public function select_for_update_global_extra( int $global_extra_id ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( 'GLOBALEXTRA' );

		if ( ! $table || empty( $global_extra_id ) ) {
			return null;
		}

		$sql    = $wpdb->prepare( "SELECT * FROM `$table` WHERE `id` = %d FOR UPDATE", $global_extra_id );
		$result = $wpdb->get_row( $sql );
		return ! empty( $result ) ? $result : null;
	}


	/**
	 * Batch-fetch global extras linked to multiple services.
	 *
	 * Returns an associative array keyed by service_id, each containing
	 * an array of global extra objects.
	 *
	 * @since 1.0.0
	 * @param array $service_ids Array of service IDs.
	 * @return array Associative array: service_id => array of global extra objects.
	 */
	public function batch_get_global_extras_for_services( array $service_ids ): array {
		global $wpdb;
		$bm_activator  = $this->get_activator();
		$mapping_table = $bm_activator->get_db_table_name( 'SERVICEGLOBALEXTRA' );
		$extras_table  = $bm_activator->get_db_table_name( 'GLOBALEXTRA' );
		$result        = array();

		if ( ! $mapping_table || ! $extras_table || empty( $service_ids ) ) {
			return $result;
		}

		// Sanitize IDs to integers only.
		$service_ids = array_map( 'absint', $service_ids );
		$service_ids = array_filter( $service_ids );

		if ( empty( $service_ids ) ) {
			return $result;
		}

		$placeholders = implode( ',', array_fill( 0, count( $service_ids ), '%d' ) );
		$sql          = $wpdb->prepare(
			"SELECT sge.`service_id`, ge.* FROM `$mapping_table` AS sge
			 INNER JOIN `$extras_table` AS ge ON sge.`global_extra_id` = ge.`id`
			 WHERE sge.`service_id` IN ($placeholders)",
			...$service_ids
		);

		$rows = $wpdb->get_results( $sql );

		if ( ! empty( $rows ) ) {
			foreach ( $rows as $row ) {
				$sid = (int) $row->service_id;
				if ( ! isset( $result[ $sid ] ) ) {
					$result[ $sid ] = array();
				}
				$result[ $sid ][] = $row;
			}
		}

		return $result;
	}


	/**
	 * Batch-fetch all services linked to each global extra in one query.
	 *
	 * Returns an associative array keyed by global_extra_id, each containing
	 * an array of objects with service_id and service_name.
	 *
	 * @since 1.0.0
	 * @param array $global_extra_ids Array of global extra IDs.
	 * @return array Associative array: global_extra_id => array of {service_id, service_name}.
	 */
	public function batch_get_services_for_global_extras( array $global_extra_ids ): array {
		global $wpdb;
		$bm_activator   = $this->get_activator();
		$mapping_table  = $bm_activator->get_db_table_name( 'SERVICEGLOBALEXTRA' );
		$services_table = $bm_activator->get_db_table_name( 'SERVICE' );
		$result         = array();

		if ( ! $mapping_table || ! $services_table || empty( $global_extra_ids ) ) {
			return $result;
		}

		$global_extra_ids = array_map( 'absint', $global_extra_ids );
		$global_extra_ids = array_filter( $global_extra_ids );

		if ( empty( $global_extra_ids ) ) {
			return $result;
		}

		$placeholders = implode( ',', array_fill( 0, count( $global_extra_ids ), '%d' ) );
		$sql          = $wpdb->prepare(
			"SELECT sge.`global_extra_id`, s.`id` AS service_id, s.`service_name`
			 FROM `$mapping_table` AS sge
			 INNER JOIN `$services_table` AS s ON sge.`service_id` = s.`id`
			 WHERE sge.`global_extra_id` IN ($placeholders)",
			...$global_extra_ids
		);

		$rows = $wpdb->get_results( $sql );

		if ( ! empty( $rows ) ) {
			foreach ( $rows as $row ) {
				$geid = (int) $row->global_extra_id;
				if ( ! isset( $result[ $geid ] ) ) {
					$result[ $geid ] = array();
				}
				$result[ $geid ][] = $row;
			}
		}

		return $result;
	}


	/**
	 * Update a row only if the current state matches the expected state.
	 *
	 * Prevents invalid state transitions (e.g. confirmed → failed).
	 *
	 * @since 1.0.0
	 * @param string $identifier         Table identifier.
	 * @param string $unique_field       Field to identify the row.
	 * @param mixed  $unique_field_value Value of the unique field.
	 * @param string $state_field        The column holding the state.
	 * @param string $expected_state     The state that must currently be set.
	 * @param array  $data               Column => value pairs to update.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public function update_if_state_matches( string $identifier, string $unique_field, $unique_field_value, string $state_field, string $expected_state, array $data ) {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		if ( ! $table ) {
			return false;
		}

		// Sanitize column names.
		$unique_field = preg_replace( '/[^a-zA-Z0-9_]/', '', $unique_field );
		$state_field  = preg_replace( '/[^a-zA-Z0-9_]/', '', $state_field );

		if ( empty( $unique_field ) || empty( $state_field ) ) {
			return false;
		}

		$where = array(
			$unique_field => $unique_field_value,
			$state_field  => $expected_state,
		);

		return $wpdb->update( $table, $data, $where );
	}


	/**
	 * Get the current value of a specific field for a row.
	 *
	 * @since 1.0.0
	 * @param string $identifier  Table identifier.
	 * @param string $field       The column to retrieve.
	 * @param mixed  $id_value    The value of the primary key or unique field.
	 * @param string $id_field    The column to match against (default: 'id').
	 * @return string|null The field value or null.
	 */
	public function get_current_state( string $identifier, string $field, $id_value, string $id_field = 'id' ): ?string {
		global $wpdb;
		$bm_activator = $this->get_activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		if ( ! $table ) {
			return null;
		}

		$field    = preg_replace( '/[^a-zA-Z0-9_]/', '', $field );
		$id_field = preg_replace( '/[^a-zA-Z0-9_]/', '', $id_field );

		if ( empty( $field ) || empty( $id_field ) ) {
			return null;
		}

		$format = is_numeric( $id_value ) ? '%d' : '%s';
		$sql    = $wpdb->prepare( "SELECT `$field` FROM `$table` WHERE `$id_field` = $format LIMIT 1", $id_value );
		$val    = $wpdb->get_var( $sql );
		return $val !== null ? (string) $val : null;
	}
}//end class
