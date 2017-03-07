<?php

defined( 'ABSPATH' ) or die();

class Silent_Publish_Test extends WP_UnitTestCase {

	protected $field    = 'silent_publish';
	protected $meta_key = '_silent-publish';
	protected $nonce    = '_silent_publish_nonce';

	private   $hooked   = -1;

	public function tearDown() {
		parent::tearDown();

		$this->hooked = -1;

		add_action( 'publish_post', '_publish_post_hook', 5, 1 );

		remove_filter( 'c2c_silent_publish_meta_key', array( $this, 'c2c_silent_publish_meta_key' ) );
		remove_filter( 'c2c_silent_publish_meta_key', '__return_empty_string' );
		remove_action( 'publish_post',                array( $this, 'check_publish_post_hook' ), 4, 1 );
		remove_action( 'publish_post',                array( $this, 'check_publish_post_hook' ), 6, 1 );
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	public function c2c_silent_publish_meta_key( $key ) {
		return '_new-key';
	}

	public function check_publish_post_hook( $post_id ) {
		$this->hooked = has_action( 'publish_post', '_publish_post_hook', 5, 1 ) ? 1 : 2;
	}


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

	public function test_save_post_filter_triggers_save_silent_publish_status() {
		$this->assertNotFalse( has_filter( 'save_post', array( 'c2c_SilentPublish', 'save_silent_publish_status' ), 2, 3 ) );
	}

	public function test_publish_post_action_triggers_publish_post() {
		$this->assertNotFalse( has_action( 'publish_post', array( 'c2c_SilentPublish', 'publish_post' ), 1, 1 ) );
	}

	public function test_non_silently_published_post_publishes_without_silencing() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		wp_publish_post( $post_id );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( 5, has_action( 'publish_post', '_publish_post_hook', 5, 1 ) );

		return $post_id;
	}

	public function test_saving_post_set_as_silently_published_retains_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );

		$post = get_post( $post_id, ARRAY_A );
		$_POST[ $this->field ] = '1';
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
		// Simulate a POST.
		$_POST[ $this->nonce ] = wp_create_nonce( $this->field );
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
	}

	public function test_saving_post_explicitly_not_being_silently_published_deletes_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		$post = get_post( $post_id, ARRAY_A );
		// Simulate a POST.
		$_POST[ $this->nonce ] = wp_create_nonce( $this->field );
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertFalse( has_action( 'publish_post', '_publish_post_hook', 5, 1 ) );
	}

	/*
	 * get_meta_key_name()
	 */

	public function test_get_meta_key_name() {
		$this->assertEquals( '_silent-publish', c2c_SilentPublish::get_meta_key_name() );
	}

	public function test_filtered_get_meta_key_name() {
		add_filter( 'c2c_silent_publish_meta_key', array( $this, 'c2c_silent_publish_meta_key' ) );

		$this->assertEquals( '_new-key', c2c_SilentPublish::get_meta_key_name() );
	}

	public function test_empty_get_meta_key_name() {
		add_filter( 'c2c_silent_publish_meta_key', '__return_empty_string' );

		$this->assertEmpty( c2c_SilentPublish::get_meta_key_name() );
	}

	public function test_silently_published_post_publishes_silently() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		// Publishing assumes it's coming from the edit page UI where the
		// checkbox is present to set the $_POST array element to trigger
		// stealth update
		$_POST[ $this->field ] = '1';
		$_POST[ $this->nonce ] = wp_create_nonce( $this->field );

		wp_publish_post( $post_id );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		return $post_id;
	}

	/*
	 * Check filter gets unhooked.
	 */

	public function test_default_behavior() {
		add_action( 'publish_post', array( $this, 'check_publish_post_hook' ), 4, 1 );

		$post_id = $this->test_non_silently_published_post_publishes_without_silencing();

		apply_filters( 'publish_post', $post_id );

		$this->assertEquals( 1, $this->hooked );
	}

	public function test_it_works() {
		add_action( 'publish_post', array( $this, 'check_publish_post_hook' ), 4, 1 );

		$post_id = $this->test_silently_published_post_publishes_silently();

		apply_filters( 'publish_post', $post_id );

		$this->assertEquals( 2, $this->hooked );
	}

}
