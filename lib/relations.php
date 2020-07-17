<?php
/**
 * Generated from 'relations.php.in' on Sun Sep 23 17:14:59 UTC 2018.
 *
 * Document relations between DOMjudge tables for various use.
 * The data is extracted from the SQL DB structure file.
 */

/** For each table specify the set of attributes that together
 *  are considered the primary key / superkey. */
$KEYS = array(
// @KEYS@
	'auditlog' => array('logid'),
	'balloon' => array('balloonid'),
	'clarification' => array('clarid'),
	'configuration' => array('configid'),
	'contest' => array('cid'),
	'contestproblem' => array('cid','probid'),
	'contestteam' => array('cid','teamid'),
	'event' => array('eventid'),
	'executable' => array('execid'),
	'internal_error' => array('errorid'),
	'judgehost' => array('hostname'),
	'judgehost_restriction' => array('restrictionid'),
	'judging' => array('judgingid'),
	'judging_run' => array('runid'),
	'language' => array('langid'),
	'problem' => array('probid'),
	'rankcache' => array('cid','teamid'),
	'rejudging' => array('rejudgingid'),
	'removed_interval' => array('intervalid'),
	'role' => array('roleid'),
	'scorecache' => array('cid','teamid','probid'),
	'submission' => array('submitid'),
	'submission_file' => array('submitfileid'),
	'team' => array('teamid'),
	'team_affiliation' => array('affilid'),
	'team_category' => array('categoryid'),
	'team_unread' => array('teamid','mesgid'),
	'testcase' => array('testcaseid'),
	'user' => array('userid'),
	'userrole' => array('userid', 'roleid'),
);

/** For each table, list all attributes that reference foreign keys
 *  and specify the source of that key. Appended to the
 *  foreign key is '&<ACTION>' where ACTION can be any of the
 *  following referential actions on delete of the foreign row:
 *  CASCADE:  also delete the source row
 *  SETNULL:  set source key to NULL
 *  RESTRICT: disallow delete of foreign row
 *  NOCONSTRAINT: no constraint is specified, even though the field
 *                references a foreign key.
 */
$RELATIONS = array(
// @RELATIONS@
	'auditlog' => array(
	),

	'balloon' => array(
		'submitid' => 'submission.submitid&CASCADE',
	),

	'clarification' => array(
		'cid' => 'contest.cid&CASCADE',
		'respid' => 'clarification.clarid&SETNULL',
		'probid' => 'problem.probid&SETNULL',
	),

	'configuration' => array(
	),

	'contest' => array(
	),

	'contestproblem' => array(
		'cid' => 'contest.cid&CASCADE',
		'probid' => 'problem.probid&CASCADE',
	),

	'contestteam' => array(
		'cid' => 'contest.cid&CASCADE',
		'teamid' => 'team.teamid&CASCADE',
	),

	'event' => array(
		'cid' => 'contest.cid&CASCADE',
	),

	'executable' => array(
	),

	'internal_error' => array(
		'judgingid' => 'judging.judgingid&SETNULL',
		'cid' => 'contest.cid&SETNULL',
	),

	'judgehost' => array(
		'restrictionid' => 'judgehost_restriction.restrictionid&SETNULL',
	),

	'judgehost_restriction' => array(
	),

	'judging' => array(
		'cid' => 'contest.cid&CASCADE',
		'submitid' => 'submission.submitid&CASCADE',
		'judgehost' => 'judgehost.hostname&RESTRICT',
		'rejudgingid' => 'rejudging.rejudgingid&SETNULL',
		'prevjudgingid' => 'judging.judgingid&SETNULL',
	),

	'judging_run' => array(
		'testcaseid' => 'testcase.testcaseid&RESTRICT',
		'judgingid' => 'judging.judgingid&CASCADE',
	),

	'language' => array(
	),

	'problem' => array(
	),

	'rankcache' => array(
	),

	'rejudging' => array(
		'userid_start' => 'user.userid&SETNULL',
		'userid_finish' => 'user.userid&SETNULL',
	),

	'removed_interval' => array(
		'cid' => 'contest.cid&CASCADE',
	),

	'role' => array(
	),

	'scorecache' => array(
	),

	'submission' => array(
		'cid' => 'contest.cid&CASCADE',
		'teamid' => 'team.teamid&CASCADE',
		'probid' => 'problem.probid&CASCADE',
		'langid' => 'language.langid&CASCADE',
		'judgehost' => 'judgehost.hostname&SETNULL',
		'origsubmitid' => 'submission.submitid&SETNULL',
		'rejudgingid' => 'rejudging.rejudgingid&SETNULL',
	),

	'submission_file' => array(
		'submitid' => 'submission.submitid&CASCADE',
	),

	'team' => array(
		'categoryid' => 'team_category.categoryid&CASCADE',
		'affilid' => 'team_affiliation.affilid&SETNULL',
	),

	'team_affiliation' => array(
	),

	'team_category' => array(
	),

	'team_unread' => array(
		'teamid' => 'team.teamid&CASCADE',
		'mesgid' => 'clarification.clarid&CASCADE',
	),

	'testcase' => array(
		'probid' => 'problem.probid&CASCADE',
	),

	'user' => array(
		'teamid' => 'team.teamid&SETNULL',
	),

	'userrole' => array(
		'userid' => 'user.userid&CASCADE',
		'roleid' => 'role.roleid&CASCADE',
	),

);

// Additional relations not encoded in the SQL structure:
$RELATIONS['clarification']['sender']    = 'team.teamid&NOCONSTRAINT';
$RELATIONS['clarification']['recipient'] = 'team.teamid&NOCONSTRAINT';

/**
 * Check whether some primary key is referenced in any
 * table as a foreign key.
 *
 * Returns an array "table name => action" of matches found.
 */
function fk_check($keyfield, $value)
{
    global $RELATIONS, $DB;

    $ret = array();
    foreach ($RELATIONS as $table => $table_rels) {
        foreach ($table_rels as $column => $constraint) {
            @list($foreign, $action) = explode('&', $constraint);
            if (empty($action)) {
                $action = 'CASCADE';
            }
            if ($foreign == $keyfield) {
                $c = $DB->q("VALUE SELECT count(*) FROM $table WHERE $column = %s", $value);
                if ($c > 0) {
                    $ret[$table] = $action;
                }
            }
        }
    }

    return $ret;
}

/**
 * Find all dependent tables that a delete could cascade into.
 *
 * Returns an array of table names.
 */
function fk_dependent_tables($table)
{
    global $RELATIONS;

    $ret = array();
    // We do a BFS through the list of tables.
    $queue = array($table);
    while (count($queue)>0) {
        $curr_table = reset($queue);
        unset($queue[array_search($curr_table, $queue)]);

        if (in_array($curr_table, $ret)) {
            continue;
        }
        if ($curr_table!=$table) {
            $ret[] = $curr_table;
        }

        foreach ($RELATIONS as $next_table => $table_rels) {
            foreach ($table_rels as $constraint) {
                @list($foreign, $action) = explode('&', $constraint);
                @list($foreign_table, $foreign_key) = explode('.', $foreign);
                if (empty($action)) {
                    $action = 'CASCADE';
                }
                if ($foreign_table==$curr_table && $action=='CASCADE') {
                    $queue[] = $next_table;
                }
            }
        }
    }

    return $ret;
}

/**
 * Given a table and column,value pair, find all rows in that table
 * where column=value and recursively any dependent rows in tables
 * that reference those. $depth is an internal variable.
 *
 * Returns in $results an array of tuples [tablename, [key => value, ...]].
 * The results are ordered such that referential integrity is
 * preserved if they are deleted as given.
 */
function fk_dependent_rows(&$results, $table, $column, $value, $depth = 0)
{
    global $RELATIONS, $KEYS, $DB;

    if (!array_key_exists($table, $KEYS)) {
        error("invalid table '$table'");
    }
    if ($depth>=10) {
        error("fk_dependent_rows recursion depth exceeded at $depth");
    }

    $keys = $KEYS[$table];
    $rows = $DB->q("TABLE SELECT %Al FROM $table WHERE `$column` = %s", $keys, $value);

    foreach ($RELATIONS as $next_table => $table_rels) {
        foreach ($table_rels as $next_column => $constraint) {
            @list($foreign, $action) = explode('&', $constraint);
            list($foreign_table, $foreign_key) = explode('.', $foreign);
            if (empty($action)) {
                $action = 'CASCADE';
            }
            if (in_array($action, array('SETNULL','NOCONSTRAINT'))) {
                continue;
            }
            if ($foreign_table == $table) {
                foreach ($rows as $row) {
                    fk_dependent_rows(
                        $results,
                        $next_table,
                        $next_column,
                                      $row[$foreign_key],
                        $depth+1
                    );
                }
            }
        }
    }

    foreach ($rows as $row) {
        $res = array($table, $row);
        if (!in_array($results, $res)) {
            $results[] = $res;
        }
    }
}
