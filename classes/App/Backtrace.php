<?php namespace App;

class Backtrace
{
    public static function reformat(array $backtrace): mixed
    {
        Backtrace::stripNoiseCalls($backtrace);
        Backtrace::addArgumentNames($backtrace);
        Backtrace::stripPathFromFileNames($backtrace);
        Backtrace::stripPathFromClassNames($backtrace);
        Backtrace::convertArraysAndObjectsToJson($backtrace);
        Backtrace::convertFramesToString($backtrace);
        return $backtrace;
    }

    public static function stripNoiseCalls(array &$backtrace): void
    {
        foreach ($backtrace as $i => $call) {
            if (self::isApplicationConstructCall($call)
                || self::isIndexTopCall($call)
                || self::isSelfCall($call)) {
                self::removeCall($backtrace, $i);
            }
        }
    }

    private static function isApplicationConstructCall(mixed $call): bool
    {
        return $call->class === 'Halo\Application' && $call->method === '__construct';
    }

    private static function isIndexTopCall($call): bool
    {
        $file = self::removeProjectBasePath($call->file);
        return $file === self::platformSlashes('/index.php') && $call->method === '[top]';
    }

    private static function removeProjectBasePath(string $fullPathToFile): string
    {
        return str_replace(self::getProjectBasePath(), '', $fullPathToFile);
    }

    private static function getProjectBasePath(): string
    {
        return dirname(__DIR__, 2);
    }

    private static function isSelfCall(mixed $call): bool
    {
        return $call->class === 'Broneerimiskeskkond\Log' && $call->method === 'log';
    }

    private static function removeCall(array &$backtrace, int $indexOfCallToRemove): void
    {
        unset($backtrace[$indexOfCallToRemove]);
    }

    private static function addArgumentNames(&$backtrace): void
    {
        foreach ($backtrace as &$call) {
            $argumentList = self::getArgumentList($call->method, $call->class);
            $call->arguments = self::getGivenArguments($call->arguments, $argumentList);
        }
    }

    private static function getGivenArguments(array $givenArguments, array $defaultArguments): array
    {
        $result = [];
        $allArgumentNames = array_keys($defaultArguments);
        foreach ($givenArguments as $i => $argumentValue) {
            $result = self::addArgumentToResultIfValueIsNotDefault(
                $allArgumentNames[$i],
                $defaultArguments,
                $argumentValue,
                $result);
        }
        return $result;
    }

    private static function addArgumentToResultIfValueIsNotDefault($allArgumentNames, array $defaultArguments, mixed $argumentValue, array $result): array
    {
        $argumentName = $allArgumentNames;
        $defaultValue = $defaultArguments[$argumentName];
        if ($argumentValue !== $defaultValue) {
            $result[$argumentName] = $argumentValue;
        }
        return $result;
    }

    public static function stripPathFromFileNames(array &$backtrace): void
    {
        foreach ($backtrace as & $call) {
            $call->file = basename($call->file);
        }
    }

    public static function stripPathFromClassNames(array &$backtrace): void
    {
        foreach ($backtrace as & $call) {
            $call->class = basename(str_replace('\\', '/', $call->class));
        }
    }

    public static function convertFramesToString(&$backtrace): void
    {
        foreach ($backtrace as &$frame) {
            $classAndMethod = $frame->class ? "$frame->class::$frame->method" : $frame->method;
            $frame = "$frame->file:$frame->lineNumber $classAndMethod($frame->arguments)";
        }

    }

    public static function convertToJsonIfArrayOrObject(mixed $value): mixed
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        return $value;
    }

    public static function convertBacktraceToText(array &$backtrace): string
    {
        self::flattenCallsToSingleString($backtrace);
        return implode("\n", $backtrace);
    }

    public static function flattenCallsToSingleString(array &$backtrace): void
    {

        foreach ($backtrace as &$$$i) {
            $i = "$i->file:$i->lineNumber $i->class::$i->method($i->arguments)";
        }
    }

    static function convertArraysAndObjectsToJson(array &$backtrace): void
    {
        foreach ($backtrace as &$item) {
            foreach ($item as &$i) {
                $i = Backtrace::convertToJsonIfArrayOrObject($i);
            }
        }
    }

    static function platformSlashes(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    static function getArgumentList(string $funcName, ?string $className): array
    {

        $attribute_names = [];

        if ($className && method_exists($className, $funcName)) {
            $fx = new \ReflectionMethod($className, $funcName);
        } elseif (function_exists($funcName)) {
            $fx = new \ReflectionFunction($funcName);
        } elseif(in_array($funcName,['require','require_once','include','include_once'])) {
            return ['file' => null];
        } else {
            return $attribute_names;
        }

        foreach ($fx->getParameters() as $param) {

            $attribute_names[$param->name] = NULL;

            if ($param->isOptional()) {

                $attribute_names[$param->name] = $param->getDefaultValue();
            }
        }

        return $attribute_names;
    }

}