<?php



/**
 * Initialize autoloader
 */
require_once('vendor/autoload.php');



/**
 * Initialize console
 */
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
$console = new Application();



/**
 * Add commands
 */
$console
    ->register('sync')
    ->setDescription('Sync destination language file - add missing keys, rearrange order.')
    ->setHelp('
The <info>sync</info> command will take the source language file and create destination
language file maintaining original content line ordering and using existing destination
language file contents if destination language file already exists.

<comment>Samples:</comment>
  Default: sync english to slovenian:
    <info>sync</info>
  Sync english to croatian:
    <info>sync croatian</info>
  Sync german to serbian:
    <info>sync --source=german sebian</info>
')

    ->setDefinition(array(
        new InputOption  ('source',      's', InputOption::VALUE_REQUIRED, 'Which language file to use as source?', 'english'),
        new InputArgument('destination',      InputArgument::OPTIONAL,     'Destination language to update',        'slovenian'),
    ))

    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $srcLang  = $input->getOption  ('source');
        $destLang = $input->getArgument('destination');

        // Switch to language directory
        chdir(realpath(__DIR__) .'/../lang/');

        // Check if source language file exists
        $srcLangFile = "$srcLang.php";
        if (!file_exists($srcLangFile)) {
            throw new Exception("Source language file not found: $srcLangFile");
        }
        $output->writeln('Using source language: <info>'. $srcLang  .'</info>');
        $output->writeln('Destination language:  <info>'. $destLang .'</info>');

        // Check if destination language file exists
        $destLangFile = "$destLang.php";
        if (!file_exists($destLangFile)) {
            $destLangFileExists = false;
            $output->writeln('Destination language file not found. Fresh start, eh? :)');
        } else {
            $destLangFileExists = true;
            $output->writeln('Destination language file already exists, will use its content.');
        }


        // Read destination language file first
        $destLangKeys = array();
        if ($destLangFileExists) {
            $destLangRaw = file($destLangFile);
            foreach ($destLangRaw as $rawLine) {
                if (!preg_match('/^([$]_LANG[^=]+)+[ ]*=[ ]*(.+)$/', $rawLine, $m)) {
                    // Skip this entry
                    continue;
                }
                $langKey    = trim($m[1]);
                $destLangVal = trim($m[2]);
                $destLangKeys[$langKey] = array(
                    'value' => $destLangVal,
                    'found' => false,
                );
            }
            $output->writeln('Existing destination language parsed, contains <info>'. count($destLangKeys) .'</info> language keys.');
        }



        // Now read source file, and generate destination file content
        $srcLangRaw = file($srcLangFile);
        $srcLangKeys = array();
        $destLangNew = "";
        foreach ($srcLangRaw as $rawLine) {
            if (!preg_match('/^([$]_LANG[^=]+)+[ ]*=[ ]*(.+)$/', $rawLine, $m)) {
                // Copy RAW entry
                $destLangNew .= "$rawLine";
                continue;
            }
            $langKey    = trim($m[1]);
            $srcLangVal = trim($m[2]);
            $srcLangKeys[$langKey] = array(
                'value'      => $srcLangVal,
                'translated' => false,
            );


            // Use existing key if it exists
            if (isset($destLangKeys[$langKey])) {
                $destLangNew .= "$langKey = ". $destLangKeys[$langKey]['value'] ."\n";
                $destLangKeys[$langKey]['found']     = true;
                $srcLangKeys[$langKey]['translated'] = true;
            } else {
                $destLangNew .= "// MISSING: $langKey = ". $srcLangVal ."\n";
            }
        }
        $output->writeln('Source language parsed, contains <info>'. count($srcLangKeys) .'</info> language keys.');



        /**
         * Obsolete translations
         */
        $obsoleteKeys = array();
        foreach ($destLangKeys as $langKey => $langValData) {
            if ($langValData['found'] == false) {
                $obsoleteKeys[$langKey] = $langValData;
            }
        }
        if (count($obsoleteKeys) > 0) {
            $output->writeln('');
            $output->writeln('WARNING: Found <question>'. count($obsoleteKeys) .'</question> obsolete keys!');
            $output->writeln('WARNING: These keys are present in destination language file, but not in source one.');
            $output->writeln('WARNING: Listing and appending to generated file:');
            $destLangNew .= "\n";
            $destLangNew .= "\n";
            $destLangNew .= "// OBSOLETE TRANSLATIONS:\n";
            foreach ($obsoleteKeys as $langKey => $langValData) {
                $output->writeln('<question>OBSOLETE</question>: '. $langKey);
                $destLangNew .= "// OBSOLETE: $langKey = ". $langValData['value'] ."\n";
            }

        }



        /**
         * Missing translations
         */
        $missingTranslations = array();
        foreach ($srcLangKeys as $langKey => $langValData) {
            if ($langValData['translated'] == false) {
                $missingTranslations[$langKey] = $langValData;
            }
        }
        if (count($missingTranslations) > 0) {
            $output->writeln('');
            $output->writeln('WARNING: Found <error>'. count($missingTranslations) .'</error> missing translations, listing:');
            foreach ($missingTranslations as $langKey => $langValData) {
                $output->writeln('<error>MISSING</error>: '. $langKey);
            }

        }



        $output->writeln("");
        $output->writeln("New language file has been written: ". $destLangFile .".new");

        file_put_contents($destLangFile. ".new", $destLangNew);
    });



/**
 * Run it
 */
$console->run();
