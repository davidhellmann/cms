<?php
namespace Blocks;

/**
 *
 */
class BaseApplicationComponent extends \CApplicationComponent
{
	// For consistency!
	/**
	 * @return bool
	 */
	public function isInitialized()
	{
		return $this->getIsInitialized();
	}
}
