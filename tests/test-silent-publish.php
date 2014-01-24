<?php

class Silent_Publish_Test extends WP_UnitTestCase {

	protected $field    = 'silent_publish';
	protected $meta_key = '_silent-publish';



	/**
	 * TESTS
	 */



	function test_non_silently_published_post_publishes_without_silencing() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		wp_publish_post( $post_id );

		$this->assertFalse( defined( 'WP_IMPORTING' ) );
	}

	function test_saving_post_set_as_silently_published_retains_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$post = get_post( $post_id, ARRAY_A );
		$post[ $this->field ] = '1';
		wp_update_post( $post );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );
	}

	function test_saving_post_without_being_silently_published_deletes_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		$post = get_post( $post_id, ARRAY_A );
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
	}

	function test_saving_post_explicitly_not_being_silently_published_deletes_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		$post = get_post( $post_id, ARRAY_A );
		$post[ $this->field ] = '';
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
	}

	/* This test must be last since it results in the WP_IMPORTING constant
	   being set. */

	function test_silently_published_post_publishes_silently() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		// Publishing assumes it's coming from the edit page UI where the
		// checkbox is present to set the $_POST array element to trigger
		// stealth update
		$_POST[ $this->field ] = '1';

		wp_publish_post( $post_id );

		$this->assertTrue( defined( 'WP_IMPORTING' ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );
	}

}
