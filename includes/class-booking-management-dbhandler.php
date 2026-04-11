<?php
class BM_DBhandler {



	public function insert_row( $identifier, $data, $format = null ) {
		global $wpdb;
		$bm_activator = new Booking_Management_Activator();
		$table        = $bm_activator->get_db_table_name( $identifier );
		$result       = $wpdb->insert( $table, $data, $format );
		if ( $result !== false ) {
			return $wpdb->insert_id;
		} else {
			return false;
		}
	}//end insert_row()


	public function update_row( $identifier, $unique_field, $unique_field_value, $data, $format = null, $where_format = null ) {
		global $wpdb;
		$bm_activator = new Booking_Management_Activator();
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
		$bm_activator = new Booking_Management_Activator();
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
		$bm_activator = new Booking_Management_Activator();
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
		$bm_activator = new Booking_Management_Activator();
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
		$bm_activator = new Booking_Management_Activator();
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
			$qry .= " LIMIT $limit OFFSET $offset";
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
		$bm_activator = new Booking_Management_Activator();
		$base_table   = $bm_activator->get_db_table_name( $tables[0] );
		$base_alias   = isset( $tables[1] ) ? $tables[1] : 's';

		if ( $increase_group_concat_length ) {
			$wpdb->query( "SET SESSION group_concat_max_len = $group_concat_length;" );
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
			$qry .= " LIMIT $limit OFFSET $offset";
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
		$bm_activator = new Booking_Management_Activator();
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
		$bm_activator = new Booking_Management_Activator();
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
	 * get results by column
	 *
	 * @author Darpan
	 */
	public function get_results_by_columns( $identifier, $columns, $exclude_columns = array(), $result_type = 'results', $output = 'OBJECT' ) {
		global $wpdb;
		$bm_activator = new Booking_Management_Activator();
		$table        = $bm_activator->get_db_table_name( $identifier );

		// Exclude specified columns
		if ( ! empty( $exclude_columns ) ) {
			$columns = array_diff( $columns, $exclude_columns );
		}

		$query = $wpdb->prepare( "SELECT $columns FROM TABLE_NAME = %s', $table" );

		if ( $result_type === 'results' ) {
			$results = $wpdb->$method_name( $query, $output );
		} else {
			$results = $wpdb->$method_name( $query );
		}

		/**$results = $wpdb->get_results( $query, ARRAY_A );*/

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


	// Helper function to convert objects to arrays
	public function object_to_array( $obj ) {
		if ( is_object( $obj ) ) {
			$obj = (array) $obj;
		}
		if ( is_array( $obj ) ) {
			$new = array();
			foreach ( $obj as $key => $val ) {
				$new[ $key ] = object_to_array( $val );
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
}//end class
