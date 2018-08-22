<?php
namespace Xlii\CbOne\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetFastDelivery implements ObserverInterface
{
    private $helper;

    private $logger;

    protected $orderItemRepository;

    public function __construct(
                                \Xlii\CbOne\Helper\Data $helper,
                                \Psr\Log\LoggerInterface $logger,
                                \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    )
    {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllItems();
        foreach($items as $item)
        {
            $qty = $item->getQtyOrdered();
            $sku = $item->getSku();
            $availability = $this->helper->getAvailability(array($sku=>$qty));
            $item->setXliiFastDelivery($availability[$sku]);
        }
    }
}