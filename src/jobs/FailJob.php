<?php
class FailJob
{
	public function perform()
	{
		callToUndefinedFunction();
	}
}