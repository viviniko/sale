<?php

namespace Viviniko\Sale\Console\Commands;

use Carbon\Carbon;
use Viviniko\Mail\Contracts\MailService;
use Viviniko\Sale\Contracts\OrderService;
use Viviniko\Sale\Enums\OrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendOrderRemainderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:remainder:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to unpaid order.';

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * $interval minutes
     * @var int
     */
    protected $interval = 10;

    /**
     * SendOrderRemainderEmails constructor.
     * @param OrderService $orderService
     * @param MailService $mailService
     */
    public function __construct(OrderService $orderService, MailService $mailService)
    {
        parent::__construct();
        $this->orderService = $orderService;
        $this->mailService = $mailService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dateRange = Carbon::now()->subMinute(2 * $this->interval)->toDateTimeString() . ' - ' . Carbon::now()->subMinute($this->interval)->toDateTimeString();
        $this->orderService->search(['status' => OrderStatus::UNPAID, 'created_at' => $dateRange])->get()->each(function ($order) {
            try {
                $this->mailService->send($order->customer_email, 'order.payment.reminder', array_merge($order->toArray(), ['products' => $order->products->toArray(), 'address' => $order->address->toArray()]));
            } catch (\Exception $e) {
                Log::error($e);
            }
        });
    }
}
