<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 return [

     "dependencies" => ["dep1","dep2"],
     "replaceables" => [
         ["pattern" => "regex", "replacement" => "replacement"]
     ],
     "removablefns" => [
         ["access" => "public", "name" => "dummyname", "content" => "dummycontent"]
     ],
     "functions" => [
 <<<EOT
 public function test() {
     \$test;
 }
 EOT
     ],
     "variables" => [
         ["access" => "public", "name" => "varname", <"value" => "some value">]
     ],
     "traits" => [
         ["name" => "traitname", "dependency" => "traitname"
     ]
 ];
