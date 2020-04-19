<?php
// Global functions

function isDevEnv() : bool
{
	return defined('DEVENV') ? DEVENV : false;
}