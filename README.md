GraphQLFieldtypeSelectExtOption
=========================

This module is currently experimental.

This module adds GraphQL support for [FieldtypeSelectExtOption](https://github.com/kixe/FieldtypeSelectExtOption). It is only intended for 
use with [ProcessGraphQL](https://github.com/dadish/ProcessGraphQL) module.

### Installation
This module's files should be placed in /site/modules/GraphQLFieldtypeSelectExtOption/

[How to install or uninstall modules](http://modules.processwire.com/install-uninstall/)

### Usage
Everything works behind the scenes for this module. After you installed it, FieldtypeSelectExtOption 
fields will be available in your GraphQL api. This module takes the MySQL datatypes of your external table and maps them to GraphQL.

| MySQL Type | GraphQL Type |
| VAR_STRING | string |
| STRING | string |
| BLOB | string |
| LONGLONG | int |
| LONG | int |
| SHORT | int |
| DATETIME | string |
| DATE | string |
| DOUBLE | float |
| TIMESTAMP | int |
| BIT | boolean |
