<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tax\Model\TaxClass\Source;

use Magento\TestFramework\Helper\ObjectManager;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Api\TaxClassRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxClassRepositoryMock;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var \Magento\Framework\Api\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Customer
     */
    protected $customer;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->taxClassRepositoryMock = $this->getMockForAbstractClass(
            'Magento\Tax\Api\TaxClassRepositoryInterface',
            ['getList'],
            '',
            false,
            true,
            true,
            []
        );
        $this->searchCriteriaBuilderMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            ['addFilter', 'create'],
            [],
            '',
            false
        );
        $this->filterBuilderMock = $this->getMock(
            'Magento\Framework\Api\FilterBuilder',
            ['setField', 'setValue', 'create'],
            [],
            '',
            false
        );

        $this->customer = $this->objectManager->getObject(
            'Magento\Tax\Model\TaxClass\Source\Customer',
            [
                'taxClassRepository' => $this->taxClassRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'filterBuilder' => $this->filterBuilderMock
            ]
        );
    }

    /**
     * Run test getAllOptions method
     *
     * @param bool $isEmpty
     * @param array $expected
     * @dataProvider dataProviderGetAllOptions
     */
    public function testGetAllOptions($isEmpty, array $expected)
    {
        $filterMock = $this->getMock(
            'Magento\Framework\Api\Filter',
            [],
            [],
            '',
            false
        );
        $searchCriteriaMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteria',
            [],
            [],
            '',
            false
        );
        $searchResultsMock = $this->getMockForAbstractClass(
            'Magento\Tax\Api\Data\TaxClassSearchResultsInterface',
            [],
            '',
            false,
            true,
            true,
            ['getItems']
        );
        $taxClassMock = $this->getMockForAbstractClass(
            'Magento\Tax\Api\Data\TaxClassInterface',
            ['getClassId', 'getClassName'],
            '',
            false,
            true,
            true
        );

        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with(\Magento\Tax\Api\Data\TaxClassInterface::KEY_TYPE)
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Tax\Api\TaxClassManagementInterface::TYPE_CUSTOMER)
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with([$filterMock])
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->taxClassRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);

        if (!$isEmpty) {
            $taxClassMock->expects($this->once())
                ->method('getClassId')
                ->willReturn(10);
            $taxClassMock->expects($this->once())
                ->method('getClassName')
                ->willReturn('class-name');

            $items = [$taxClassMock];
            $searchResultsMock->expects($this->once())
                ->method('getItems')
                ->willReturn($items);

            // checking of a lack of re-initialization
            for ($i = 10; --$i;) {
                $result = $this->customer->getAllOptions();
                $this->assertEquals($expected, $result);
            }
        } else {
            $items = [];
            $searchResultsMock->expects($this->once())
                ->method('getItems')
                ->willReturn($items);
            // checking exception
            $this->assertEmpty($this->customer->getAllOptions());
        }
    }

    /**
     * Data provider for testGetAllOptions
     *
     * @return array
     */
    public function dataProviderGetAllOptions()
    {
        return [
            ['isEmpty' => false, 'expected' => [['value' => 10, 'label' => 'class-name']]],
            ['isEmpty' => true, 'expected' => []]
        ];
    }
}
