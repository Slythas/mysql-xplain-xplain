<?php
require '../app/base.php';

use \Jasny\MySQL\DB as DB;
use \Jasny\MySQL\DB_Exception as DB_Exception;

use \Rap2hpoutre\MySQLExplainExplain\Explainer as Explainer;

$query = '';
$explainer = null;

if (isset($_SESSION['mysql'])) {
	new DB(
		$_SESSION['mysql']['host'], 
		$_SESSION['mysql']['user'], 
		$_SESSION['mysql']['password'], 
		$_SESSION['mysql']['base']
	);
	$mysql_version = mb_substr(DB::conn()->server_info,0,3);
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$query = $_POST['query'];
		try {
			$explain_results = DB::conn()->fetchAll(
				(strpos(strtolower($query), 'explain') === false ? 'EXPLAIN ' : '') . $_POST['query']
			);
			$explainer = new Explainer($mysql_version);
			$explainer->setResults($explain_results);
		} catch (DB_Exception $e) {
			$template->error = utf8_encode($e->getError());
		}
	}
} else {
	header('Location: config.php');
	exit;
}
// Affichage
$template->page = 'Home';
$template->explainer = $explainer;
$template->query = $query;
$template->mysql_base_doc_url = MYSQL_DOC_URL . $mysql_version . '/en/explain-output.html';
echo $template->render('home');