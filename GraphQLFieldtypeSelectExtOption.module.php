<?php

namespace ProcessWire;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use ProcessWire\GraphQL\Cache;
use ProcessWire\Page;
use ProcessWire\Field;
use ProcessWire\GraphQL\Utils;

class GraphQLFieldtypeSelectExtOption extends WireData implements Module
{

  public static function getModuleInfo()
  {

    return array(
      'title' => 'GraphQLFieldtypeSelectExtOption',
      'version' => '1.0.0',
      'summary' => 'GraphQL support for FieldtypeSelectExtOption.',
      'href' => 'https://github.com/blue-tomato/GraphQLFieldtypeSelectExtOption',
      'requires' => ['ProcessGraphQL', 'FieldtypeSelectExtOption']
    );
  }

  public static $name = 'SelectExtOption';
  public static $inputName = 'SelectExtOptionInput';

  private static function _translateNativeType($orig)
  {
    $trans = array(
      'VAR_STRING' => Type::string(),
      'STRING' => Type::string(),
      'BLOB' => Type::string(),
      'LONGLONG' => Type::int(),
      'LONG' => Type::int(),
      'SHORT' => Type::int(),
      'DATETIME' => Type::string(),
      'DATE' => Type::string(),
      'DOUBLE' => Type::float(),
      'TIMESTAMP' => Type::int(),
      'FLOAT' => Type::float(),
      'BIT' => Type::boolean()
    );
    return $trans[$orig] ?? Type::string(); //fallback string
  }

  private static function _getSelectExtOptFields(Field $field)
  {
    $db = wire('database');
    $dbTable = $field->data['option_table'];
    $result = $db->query("SELECT * FROM `$dbTable` LIMIT 0");
    return $result;
  }

  public static function getType(Field $field)
  {
    $fields = (array) [];
    $result = self::_getSelectExtOptFields($field);
    $columnCount = $result->columnCount();
    for ($i = 0; $i < $columnCount; $i++) {
      $column = $result->getColumnMeta($i);

      $name = (string) $column['name'];
      $type = self::_translateNativeType($column['native_type']);

      if ($name && !empty($name)) {
        $fields[$name] = [
          'type' => $type,
          'description' => "The $name column field of the SelectExtOption table",
          'resolve' => function ($value) use ($name) {
            return $value[$name];
          }
        ];
      }
    }

    $type = Cache::type(self::$name, function () use ($fields) {
      return new ObjectType([
        'name' => self::$name,
        'fields' => $fields
      ]);
    });

    if (self::isMultiple($field)) {
      return Type::listOf($type);
    }

    return $type;
  }

  public static function getInputType(Field $field)
  {
    $fields = (array) [];
    $result = self::_getSelectExtOptFields($field);
    $columnCount = $result->columnCount();
    for ($i = 0; $i < $columnCount; $i++) {
      $column = $result->getColumnMeta($i);

      $name = (string) $column['name'];
      $type = self::_translateNativeType($column['native_type']);

      if ($name && !empty($name)) {
        $fields[$name] = $type;
      }
    }

    $type = Cache::type(self::getName($field), function () use ($fields, $field) {
      return new InputObjectType([
        'name' => "{$field->name}" . self::$inputName,
        'fields' => $fields
      ]);
    });

    if (self::isMultiple($field)) {
      return Type::listOf($type);
    }

    return $type;
  }

  public static function setValue(Page $page, Field $field, $value)
  {
    $fieldName = $field->name;
    $result = self::_getSelectExtOptFields($field);
    $columnCount = $result->columnCount();
    for ($i = 0; $i < $columnCount; $i++) {
      $column = $result->getColumnMeta($i);
      $name = (string) $column['name'];
      $page->$fieldName->$name = $value[$name];
    }
  }

  private static function getName(Field $field = null)
  {
    if ($field instanceof Field) {
      return Utils::normalizeTypeName("{$field->name}" . self::$inputName);
    }

    return self::$inputName;
  }

  public static function isMultiple(Field $field = null)
  {
    if($field && in_array($field->input_type, ["InputfieldSelectMultiple", "InputfieldCheckboxes", "InputfieldAsmSelect"])) {
      return true;
    }

    return false;
  }

}
