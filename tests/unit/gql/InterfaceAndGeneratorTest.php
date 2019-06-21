<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craftunit\gql;

use Codeception\Test\Unit;
use Craft;
use craft\base\Element as BaseElement;
use craft\elements\Asset as AssetElement;
use craft\elements\Entry as EntryElement;
use craft\elements\GlobalSet as GlobalSetElement;
use craft\elements\MatrixBlock as MatrixBlockElement;
use craft\elements\User as UserElement;
use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\elements\Asset as AssetInterface;
use craft\gql\interfaces\elements\Element as ElementInterface;
use craft\gql\interfaces\elements\Entry as EntryInterface;
use craft\gql\interfaces\elements\GlobalSet as GlobalSetInterface;
use craft\gql\interfaces\elements\MatrixBlock as MatrixBlockInterface;
use craft\gql\interfaces\elements\User as UserInterface;
use craft\gql\TypeLoader;
use craft\gql\types\generators\TableRowType;
use crafttests\fixtures\AssetsFixture;
use crafttests\fixtures\EntryWithFieldsFixture;
use crafttests\fixtures\GlobalSetFixture;
use crafttests\fixtures\GqlTokensFixture;
use crafttests\fixtures\UsersFixture;
use GraphQL\Type\Definition\ObjectType;

class InterfaceAndGeneratorTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $gqlService = Craft::$app->getGql();
        $token = $gqlService->getTokenByAccessToken('My+voice+is+my+passport.+Verify me.');
        $gqlService->setToken($token);
    }

    protected function _after()
    {
        Craft::$app->getGql()->flushCaches();
    }

    public function _fixtures()
    {
        return [
            'assets' => [
                'class' => AssetsFixture::class
            ],
            'entries' => [
                'class' => EntryWithFieldsFixture::class
            ],
            'globalSets' => [
                'class' => GlobalSetFixture::class
            ],
            'users' => [
                'class' => UsersFixture::class
            ],
            'gqlTokens' => [
                'class' => GqlTokensFixture::class
            ],
        ];
    }

    // Tests
    // =========================================================================

    /**
     * Test interfaces running type generators.
     * @group gql
     *
     * @dataProvider interfaceDataProvider
     *
     * @param string $gqlInterfaceClass The interface class being tested
     * @param callable $getAllContexts The callback that provides an array of all contexts for generated types
     * @param callable $getTypeNameByContext The callback to generate the GQL type name by context
     */
    public function testInterfacesGeneratingTypes(string $gqlInterfaceClass, callable $getAllContexts, callable $getTypeNameByContext)
    {
        $gqlInterfaceClass::getType();

        foreach ($getAllContexts() as $context) {
            $typeName = $getTypeNameByContext($context);
            $this->assertNotFalse(GqlEntityRegistry::getEntity($typeName));
            $this->assertInstanceOf(ObjectType::class, TypeLoader::loadType($typeName));
        }
    }

    /**
     * Test table row generator
     * @group gql
     *
     * @throws \craft\errors\GqlException
     */
    public function testTableRowTypeGenerator()
    {
        $tableField = Craft::$app->getFields()->getFieldByHandle('appointments');
        TableRowType::generateTypes($tableField);
        $typeName = TableRowType::getName($tableField);
        $this->assertNotFalse(GqlEntityRegistry::getEntity($typeName));
        $this->assertInstanceOf(ObjectType::class, TypeLoader::loadType($typeName));
    }

    
    // Data providers
    // =========================================================================

    public function interfaceDataProvider(): array
    {
        return [
            [AssetInterface::class, function () { return [Craft::$app->getVolumes()->getVolumeByUid('volume-1000-uid')];}, [AssetElement::class, 'getGqlTypeNameByContext']],
            [ElementInterface::class, function () {return ['Element'];}, [BaseElement::class, 'getGqlTypeNameByContext']],
            [EntryInterface::class, function () { return [Craft::$app->getSections()->getEntryTypeById(1000)];}, [EntryElement::class, 'getGqlTypeNameByContext']],
            [GlobalSetInterface::class, function () { return [Craft::$app->getGlobals()->getSetByHandle('aGlobalSet')];}, [GlobalSetElement::class, 'getGqlTypeNameByContext']],
            [MatrixBlockInterface::class, function () { return Craft::$app->getMatrix()->getAllBlockTypes();}, [MatrixBlockElement::class, 'getGqlTypeNameByContext']],
            [UserInterface::class, function () {return ['User'];}, [UserElement::class, 'getGqlTypeNameByContext']],
        ];
    }
}
