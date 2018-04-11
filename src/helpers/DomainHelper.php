<?php

namespace yii2lab\domain\helpers;

use Yii;
use yii\base\InvalidArgumentException;
use yii2lab\domain\BaseEntity;
use yii2lab\domain\Domain;
use yii2lab\helpers\ClassHelper;
use yii2lab\helpers\Helper;
use yii2mod\helpers\ArrayHelper;

class DomainHelper {
	
	/**
	 * @param $domainId
	 * @param $definition
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function define($domainId, $definition) {
		$definition = ConfigHelper::normalizeItemConfig($domainId, $definition);
		if(!Yii::$domain->has($domainId)) {
			Yii::$domain->set($domainId, $definition);
		}
	}
	
	/**
	 * @param string     $domainId
	 * @param            $className
	 * @param array|null $classDefinition
	 *
	 * @return array|mixed|null
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getClassConfig(string $domainId, $className, array $classDefinition = null) {
		$definition = self::getConfigFromDomainClass($className);
		$definition = ConfigHelper::normalizeItemConfig($domainId, $definition);
		if(!empty($classDefinition)) {
			$classDefinition =  ConfigHelper::normalizeItemConfig($domainId, $classDefinition);
			$definition = ArrayHelper::merge($definition, $classDefinition);
		}
		$definition['class'] = $className;
		return $definition;
	}
	
	/**
	 * @param $className
	 *
	 * @return array
	 * @throws \yii\base\InvalidConfigException
	 */
	private static function getConfigFromDomainClass($className) {
		$definition = ClassHelper::normalizeComponentConfig($className);
		/** @var Domain $domain */
		$domain = Yii::createObject($definition);
		$config = $domain->config();
		return $config;
	}
	
	public static function isEntity($data) {
		return is_object($data) && $data instanceof BaseEntity;
	}
	
	public static function isCollection($data) {
		return is_array($data);
	}
	
	/**
	 * @param $name
	 *
	 * @return bool
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function has($name) {
		if(empty($name)) {
			throw new InvalidArgumentException('Domain name can not be empty!');
		}
		if(!Yii::$domain->has($name)) {
			return false;
		}
		$domain = Yii::$domain->get($name);
		if(!$domain instanceof Domain) {
			return false;
		}
		return true;
	}
	
	public static function messagesAlias($bundleName) {
		if(!Yii::$domain->has($bundleName)) {
			return false;
		}
		$domain = ArrayHelper::getValue(Yii::$domain, $bundleName);
		if(empty($domain) || empty($domain->path)) {
			return null;
		}
		return Helper::getBundlePath($domain->path . SL . 'messages');
	}
	
}