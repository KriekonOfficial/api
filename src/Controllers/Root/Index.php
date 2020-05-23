<?php

namespace Controllers\Root;

use Core\Controller;
use Core\Response\ErrorResponse;

class Index extends Controller
{
	public function index()
	{
		return new ErrorResponse(400, 'Please select an endpoint to query.');
	}
}