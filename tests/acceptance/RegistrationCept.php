<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('залогиниться');
$I->amOnPage('/');
$I->click('Войти');
$I->click('Регистрация');
$I->fillField(['id'=>'name'],'Codeception test');
$I->fillField(['id'=>'username'],'ctest');
$I->fillField(['id'=>'email'],'design@robozap.ru');
$I->fillField(['id'=>'email2'],'design@robozap.ru');
$I->fillField(['id'=>'password'],'masterkey');
$I->fillField(['id'=>'password2'],'masterkey');
$I->attachFile(['id'=>'image'], 'user.png');
$I->click('Зарегистрироваться');
$I->seeInDatabase('robo_users', array('name'=>'Codeception test','username'=>'ctest1','email'=>'design@robozap.ru'));
