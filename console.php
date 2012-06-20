<?php

require_once('vendor/autoload.php');
include ('vendor/phpactiveresource/phpactiveresource/ActiveResource.php');

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application();

class Issue extends ActiveResource {
  var $request_format = 'xml'; // REQUIRED!

  function __construct($host = '', $user = '', $password = '', $data = array()) {
    $this->password = $password;
    $this->user     = $user;
    $this->site     = $host;

    parent::__construct($data);
  }
}

$console
  ->register('issues:list')
  ->setDefinition(array(
      new InputArgument('host',     InputArgument::REQUIRED, 'Redmine host name. Add trailing slash'),
      new InputArgument('user',     InputArgument::REQUIRED, 'User authentication username'),
      new InputArgument('password', InputArgument::REQUIRED, 'User authentication password'),
      new InputArgument('project',  InputArgument::OPTIONAL, 'Limit to a specific project', NULL),
    ))
  ->setDescription('List issues.')
  ->setHelp('
The <info>issues:list</info> command will list all issues.

<comment>Samples:</comment>
  To run:
    <info>php console.php issues:list http://<question>HOSTNAME</question>/ <question>USERNAME</question> <question>PASSWORD</question></info>')
  ->setCode(function (InputInterface $input, OutputInterface $output) {
    $host = $input->getArgument('host');
    $user = $input->getArgument('user');
    $password = $input->getArgument('password');
    $project = $input->getArgument('project');

    $issue = new Issue($host, $user, $password);
    $issues = $issue->find('all');

    $tbl = new Console_Table();
    $tbl->setHeaders(
        array('ID', 'Reporter', 'Subject')
    );

    foreach ($issues as $issue) {
      $tbl->addRow(array(
        $issue->id,
        $issue->author['name'],
        $issue->subject
      ));
    }
    $output->write($tbl->getTable());
  });

$console->run();
