<?php

class ActivateTempFiles extends BuildTask
{
	public function run($request)
	{
		$iterator = new RegexIterator(
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(ASSETS_PATH)
			),
			'/^.+\.delay\.tmp$/i',
			RecursiveRegexIterator::GET_MATCH
		);

		foreach ($iterator as $value) {
			$inFile = $value[0];
			$outFile = str_replace('.delay.tmp', '', $inFile);

			echo "Renaming $inFile to $outFile...<br>\n";
			if (!rename($inFile, $outFile)) {
				throw new \LogicException("Couldn't rename $inFile to $outFile");
			}
		}

	}
}
