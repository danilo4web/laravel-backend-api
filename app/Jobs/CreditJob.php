<?php

namespace App\Jobs;

use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\CheckLogRepositoryInterface;
use App\Repositories\Contracts\CheckRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreditJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public array $data;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 25;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        TransactionRepositoryInterface $transactionRepository,
        AccountRepositoryInterface $accountRepository,
        CheckLogRepositoryInterface $checkLogRepository,
        CheckRepositoryInterface $checkRepository
    ) {
        $checkArray = $checkRepository->find($this->data['check_id'])->toArray();
        $checkArray['status'] = 'approved';
        $checkRepository->update($this->data['check_id'], $checkArray);

        $checkLogRepository->store(
            [
            'admin_id' => $this->data['admin_id'],
            'check_id' => $this->data['check_id'],
            'status' => 'approved'
            ]
        );

        $transactionRepository->store(
            [
            'amount' => $checkArray['amount'],
            'description' => 'Credit: ' . $checkArray['description'],
            'check_id' => $checkArray['id'],
            'account_id' => $checkArray['account_id'],
            'type' => 'credit'
            ]
        );

        $accountRepository->addCredit($checkArray['account_id'], $checkArray['amount']);
    }
}
