<?php

/**
 * This file adds the Transfer trait to Unicodeveloper\Paystack package
 * Structured according to paystack v2.0 api documentation
 * https://developers.paystack.co/v2.0/reference
 * 
 * (c) Samuel Ndubuisi <samniwebdev@gmail.com>
 * 
 */


 namespace Unicodeveloper\Paystack\Traits;

trait Transfer {

  // Implements tranfer control  endpoints
  // use TransferControl;

  /**
   * Creates a new recipient. 
   * A duplicate account number will lead to the retrieval of the existing record.
   * 
   * @param string $type
   * @param string $name
   * @param string $account_number
   * @param string $bank_code
   * @param string $curreny
   * @param string $description
   * @param string $authorization_code
   * @param object $metadata
   * 
   * @return array
   */

  public function createTransferRecipient($data = [])
  {

    if(empty($data)) {
      $data = [
        'type' => request()->type ?? '',
        'name' => request()->name ?? '',
        'account_number' => request()->account_number ?? '',
        'bank_code' => request()->bank_code ?? '',
        'curreny' => request()->curreny ?? '',
        'description' => request()->description ?? '',
        'authorization_code' => request()->authorization_code ?? '',
        'metadata' => request()->metadata ?? null,
      ];
    }

    $this->setRequestOptions();
    return $this->setHttpResponse('/transferrecipient', 'POST', $data)->getResponse();
  }

  /**
   * Retrieves list of existing transfer recipients
   * 
   * @param int $perPage
   * @param int $page
   * 
   * @return object
   */
  public function listTransferRecipients($page = null, $perPage = null)
  {

    $query = [];
    if(request()->page || $page) 
      $query['page'] = request()->page ?? $page;

    if(request()->perPage || $perPage)
      $query['perPage'] = request()->perPage ?? $perPage;

    $this->setRequestOptions();

    return $this->setHttpResponse('/transferrecipient', 'GET', null, $query);
    
  }

  /**
   * Initiate fund transfer from balance to saved recipient
   * 
   * @param string  $source 
   * @param integer $amount
   * @param string  $currency
   * @param string  $reason
   * @param string  $recipient
   * @param string  $reference
   * 
   * @return object
   */
  public function initiateTransfer($data = [])
  {
    if(empty($data)) {
      $data = [
        'source' => request()->source ?? '',
        'amount' => request()->amount ?? '',
        'currency' => request()->currency ?? '',
        'reason' => request()->reason ?? '',
        'recipient' => request()->recipient ?? '',
        'reference' => request()->reference ?? ''
      ];
    }

    $this->setRequestOptions();
    return $this->setHttpResponse('/transfer', 'POST', $data)->getResponse();    
  }


  /**
   * Initiate transfer to multiple recipients
   * Transfer OTP should be disabled to use this feature
   * 
   * @param string $source
   * @param string $currency
   * @param array $transfers 
   *    - transfers contain amount and recipeint objects {"amount":  int, "recipient": string}
   * @return object
   * 
   * 
   */
  public function initiateBulkTransfer($data = [])
  {
    if(empty($data)) {
      $data = [
        'source' => request()->source ?? '',
        'currency' => request()->currency ?? '',
        'transfers' => request()->transfers ?? []
      ];
    }

    $this->setRequestOptions();
    return $this->setHttpResponse('/transfer/bulk', 'POST', $data)->getResponse();
    
  }


  /**
   * Finalize pending transfer 
   * This is required if OTP is required to complete transfer
   * 
   * @param string $transfer_code
   * @param string $otp
   * 
   * @return object
   * 
   */
  public function finalizeTransfer($data = [])
  {
    if(empty($data)) {
      $data = [
        'transfer_code' => request()->transfer_code ?? '',
        'otp' => request()->otp ?? ''
      ];
    }

    $this->setRequestOptions();
    return $this->setHttpResponse('/transfer/finalize_transfer', 'POST', $data)->getResponse();
    
  }



  /**
   * Verify transfer status
   * 
   * @param string $reference
   * 
   * @return object
   */
  public function verifyTransfer($reference = null)
  {
    $transferReference = $reference ?? request()->reference;

    $this->setRequestOptions();
    return $this->setHttpResponse('/transfer/verify/' . $transferReference, 'GET')->getResponse();

  }


  /**
   * Check if transfer is valid
   * 
   * @param string $reference
   * 
   * @return bool
   */

  public function isTransferValid($reference = null)
  {
    $response = $this->verifyTransfer($reference);

    return (bool) $response['status'];
  }


  /**
   * Retrieves list of existing transfers
   * 
   * @param int $perPage
   * @param int $page
   * 
   * @return object
   */

  public function listTransfers($page = null, $perPage = null)
  {
    $query = [];
    if(request()->page || $page) 
      $query['page'] = request()->page ?? $page;

    if(request()->perPage || $perPage)
      $query['perPage'] = request()->perPage ?? $perPage;

    $this->setRequestOptions();

    return $this->setHttpResponse('/transfer', 'GET', null, $query);
    
  }


  /**
   * Retrieve transfer details using transfer id or code
   * 
   * @param string $id_or_code
   * 
   * @return object
   */

  public function fetchTransfer($id_or_code = null)
  {
    $refCode = $id_or_code ?? request()->id_or_code;

    $this->setRequestOptions();
    return $this->setHttpResponse('/transfer/' . $refCode, 'GET')->getResponse();
  }

}