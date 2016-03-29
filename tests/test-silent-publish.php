<?php

defined( 'ABSPATH' ) or die();

class Silent_Publish_Test extends WP_UnitTestCase {

	protected $field    = 'silent_publish';
	protected $meta_key = '_silent-publish';


	//
	//
	// TESTS
	//
	//


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_SilentPublish' ) );
	}

	public function test_version() {
		$this->assertEquals( '2.5', c2c_SilentPublish::version() );
	}

	public function test_init_action_triggers_do_init() {
		$this->assertNotFalse( has_filter( 'init', array( 'c2c_SilentPublish', 'do_init' ) ) );
	}

	public function test_post_submitbox_misc_action_triggers_add_ui() {
		$this->assertNotFalse( has_action( 'post_submitbox_misc_actions', array( 'c2c_SilentPublish', 'add_ui' ) ) );
	}

	public function test_wp_insert_post_data_filter_triggers_save_silent_publish_status() {
		$this->assertNotFalse( has_filter( 'wp_insert_post_data', array( 'c2c_SilentPublish', 'save_silent_publish_status' ), 2, 2 ) );
	}

	public function test_publish_post_action_triggers_publish_post() {
		$this->assertNotFalse( has_action( 'publish_post', array( 'c2c_SilentPublish', 'publish_post' ), 1, 1 ) );
	}

	public function test_non_silently_published_post_publishes_without_silencing() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		wp_publish_post( $post_id );

		$this->assertFalse( defined( 'WP_IMPORTING' ) );
	}

	public function test_saving_post_set_as_silently_published_retains_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$post = get_post( $post_id, ARRAY_A );
		$post[ $this->field ] = '1';
		wp_update_post( $post );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );
	}

	public function test_saving_post_without_being_silently_published_deletes_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		$post = get_post( $post_id, ARRAY_A );
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
	}

	public function test_saving_post_explicitly_not_being_silently_published_deletes_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		$post = get_post( $post_id, ARRAY_A );
		$post[ $this->field ] = '';
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
	}

	/* This test must be last since it results in WP_IMPORTING constant being set. */

	public function test_silently_published_post_publishes_silently() {
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
