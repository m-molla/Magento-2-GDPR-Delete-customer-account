<?php
/**
 * Customer Account Delete Post Controller
 * @author Montu Molla <m.molla@lutehc.it>
 */
namespace Lutech\DeleteAccount\Controller\Account;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;

class DeletePost extends \Magento\Framework\App\Action\Action
{
    /*
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */

    protected $_customerSession;

    /*
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /*
     * @var \Magento\Customer\Model\AuthenticationInterface 
     */
    protected $authenticationInterface;

    /*
     * @var \Magento\Customer\Api\CustomerRepositoryInterface 
     */
    protected $customerRepositoryInterface;
    
    /*
     * @var \Magento\Framework\Registry 
     */
    protected $registry;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\AuthenticationInterface $authenticationInterface
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\AuthenticationInterface $authenticationInterface,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->authenticationInterface = $authenticationInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->registry = $registry;
    }

    
    /**
     * Execute action based on request and return result
     */
    public function execute() {

        $customerId = $this->_customerSession->getCustomerId();
        $password = $this->getRequest()->getParam('password');

        if (!$this->formKeyValidator->validate($this->getRequest()) || $customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting your account.'));
        } else {
            try {
                $this->authenticate($customerId, $password);
            } catch (\Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
                return $this->_redirect($this->_redirect->getRefererUrl());
            }

            try {
                $this->registry->register('isSecureArea', true, true);
                $this->customerRepositoryInterface->deleteById($customerId);
                $this->messageManager->addSuccessMessage(__('Account Delete Successfully'));
            } catch (Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
                return $this->_redirect('customer/account/');
            }
        }

        $this->_redirect('/');
    }

    /**
     * Authenticate Customer by Password
     * @param int $customerId
     * @param String $password
     * @throws InvalidEmailOrPasswordException
     */
    protected function authenticate($customerId, $password) {
        try {
            $this->authenticationInterface->authenticate($customerId, $password);
        } catch (InvalidEmailOrPasswordException $ex) {
            throw new InvalidEmailOrPasswordException(__("Account delete fail. Invalid Password"));
        }
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request) {
        if (!$this->_customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

}
