<?php

namespace craft\conditions\elements\assets;

use Craft;
use craft\conditions\BaseNumberConditionRule;
use craft\conditions\QueryConditionRuleInterface;
use craft\elements\db\AssetQuery;
use craft\helpers\Cp;
use craft\helpers\Html;
use yii\base\InvalidValueException;
use yii\db\QueryInterface;

/**
 * File Size condition rule.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 4.0.0
 */
class FileSizeConditionRule extends BaseNumberConditionRule implements QueryConditionRuleInterface
{
    const UNIT_B = 'B';
    const UNIT_KB = 'KB';
    const UNIT_MB = 'MB';
    const UNIT_GB = 'GB';

    /**
     * @var string The size unit
     */
    public string $unit = self::UNIT_B;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Craft::t('app', 'File Size');
    }

    /**
     * @inheritdoc
     */
    public function getHtml(array $options = []): string
    {
        return Html::tag('div',
            parent::getHtml($options) .
            Cp::selectHtml([
                'name' => 'unit',
                'options' => [
                    ['value' => self::UNIT_B, 'label' => self::UNIT_B],
                    ['value' => self::UNIT_KB, 'label' => self::UNIT_KB],
                    ['value' => self::UNIT_MB, 'label' => self::UNIT_MB],
                    ['value' => self::UNIT_GB, 'label' => self::UNIT_GB],
                ],
                'value' => $this->unit,
            ]),
            [
                'class' => ['flex', 'flex-nowrap'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getExclusiveQueryParams(): array
    {
        return ['size'];
    }

    /**
     * @inheritdoc
     */
    public function modifyQuery(QueryInterface $query): void
    {
        /** @var AssetQuery $query */
        if ($this->unit === self::UNIT_B) {
            $query->size($this->paramValue());
            return;
        }

        if (!$this->value) {
            return;
        }

        $multiplier = 1;

        switch ($this->unit) {
            case self::UNIT_GB:
                $multiplier *= 1000;
            // no break
            case self::UNIT_MB:
                $multiplier *= 1000;
            // no break
            case self::UNIT_KB:
                $multiplier *= 1000;
                break;
            default:
                throw new InvalidValueException("Invalid file size unit: $this->unit");
        }

        // 1 KB == 500 - 1,499 B
        $maxDiff = $multiplier / 2;
        $minBytes = $this->value * $multiplier - $maxDiff;
        $maxBytes = $this->value * $multiplier + $maxDiff - 1;

        switch ($this->operator) {
            case self::OPERATOR_EQ:
                $query->size(['and', ">= $minBytes", "<= $maxBytes"]);
                return;
            case self::OPERATOR_NE:
                $query->size(['and', "< $minBytes", "> $maxBytes"]);
                return;
            case self::OPERATOR_LT:
                $query->size("< $minBytes");
                return;
            case self::OPERATOR_LTE:
                $query->size("<= $maxBytes");
                return;
            case self::OPERATOR_GT:
                $query->size("> $maxBytes");
                return;
            case self::OPERATOR_GTE:
                $query->size(">= $minBytes");
                return;
            default:
                throw new InvalidValueException("Invalid file size operator: $this->operator");
        }
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [
            ['unit'], 'in', 'range' => [
                self::UNIT_B,
                self::UNIT_KB,
                self::UNIT_MB,
                self::UNIT_GB,
            ]
        ];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return array_merge(parent::getConfig(), [
            'unit' => $this->unit,
        ]);
    }
}