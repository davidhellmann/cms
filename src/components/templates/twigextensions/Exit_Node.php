<?php
namespace Blocks;

/**
 *
 */
class Exit_Node extends \Twig_Node
{
	/**
	 * Compiles a Exit_Node into PHP.
	 */
	public function compile(\Twig_Compiler $compiler)
	{
		$compiler->addDebugInfo($this);

		if ($status = $this->getNode('status'))
		{
			$compiler
				->write('throw new \Blocks\HttpException(')
				->subcompile($status)
				->raw(");\n");
		}
		else
		{
			$compiler
				->write("\Blocks\blx()->end();\n");
		}
	}
}
