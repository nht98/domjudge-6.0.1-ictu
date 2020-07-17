-- This script upgrades table structure, data, and privileges
-- from/to the exact version numbers specified in the filename.

--
-- First execute a check whether this upgrade should apply. The check
-- below should fail if this upgrade has already been applied, but
-- keep everything unchanged if not.
--

-- @UPGRADE-CHECK@
ALTER TABLE `contest` ADD  COLUMN `activatetime_string` varchar(20) NOT NULL;
ALTER TABLE `contest` DROP COLUMN `activatetime_string`;
-- This upgrade can always be applied without problems, but we still
-- check at least that our version < 3.2.

--
-- Create additional structures
--

ALTER TABLE `judging`
  MODIFY COLUMN `output_compile` blob COMMENT 'Output of the compiling the program';

ALTER TABLE `judging_run`
  MODIFY COLUMN `output_run` blob COMMENT 'Output of running the program',
  MODIFY COLUMN `output_diff` blob COMMENT 'Diffing the program output and testcase output',
  MODIFY COLUMN `output_error` blob COMMENT 'Standard error output of the program';

--
-- Add/remove privileges
--

FLUSH PRIVILEGES;

--
-- Transfer data from old to new structure
--

--
-- Add/remove sample/initial contents
--

--
-- Finally remove obsolete structures after moving data
--

