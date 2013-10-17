<?php

CroogoRouter::connect('/terms/:slugs/*', array('plugin' => 'multiterm', 'controller' => 'multiterms', 'action' => 'view'));

?>
