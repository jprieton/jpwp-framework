<?php

function add_term_meta($term_id, $meta_key, $meta_value, $unique = FALSE) {
	return add_metadata('term', $term_id, $meta_key, $meta_value, $unique);
}

function get_term_meta($term_id, $meta_key = '', $single = FALSE) {
	return get_metadata('term', $term_id, $meta_key, $single);
}

function delete_term_meta($term_id, $meta_key, $meta_value = '', $delete_all = FALSE) {
	return delete_metadata('term', $term_id, $meta_key, $meta_value, $delete_all);
}

function update_term_meta($term_id, $meta_key, $meta_value, $prev_value = '') {
	return update_metadata('term', $term_id, $meta_key, $meta_value, $prev_value);
}
