<?php


class Users {

	private $db;

	public function __construct( Nette\Database\Context $db ) {
		$this->db = $db;
	}

	public function findByGoogleId($id) {
		$data = $this->db->table( 'users' )
			->wherePrimary( $id )
			->fetch();

		if ( $data ) {
			return $this->getUserDataAsObject( $data );
		} else {
			return FALSE;
		}
	}

	public function findByToken($token) {
		$data = $this->db->table( 'users' )
			->where( 'token', $token )
			->fetch();

		if ( $data ) {
			return $this->getUserDataAsObject( $data );
		} else {
			return FALSE;
		}
	}

	private function getUserDataAsObject($data) {
		return (object) array(
			"id" => $data["id"],
			"name" => $data["name"],
			"email" => $data["email"],
			"picture" => $data["picture"],
			"token" => $data["token"],
		);
	}

	public function registerFromGoogle($id, $data) {
		$data = array(
			"id" => $data["id"],
			"name" => $data["name"],
			"email" => $data["email"],
			"picture" => $data["picture"],
			"token" => \Nette\Utils\Random::generate(32)
		);

		$this->db->table( 'users' )
			->insert( $data );

		return $this->findByGoogleId($id);
	}

	public function updateGoogleAccessToken($id, $accessToken) {
		return $this->db->table( 'users' )
			->wherePrimary( $id )
			->update( array( 'access_token' => $accessToken ) );
	}

}
