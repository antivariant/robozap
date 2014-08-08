<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('see if robozap and all home sections live');
$I->amOnPage("/");
$I->see("НОВОСТИ ИЗ ЛАБОРАТОРИИ");
