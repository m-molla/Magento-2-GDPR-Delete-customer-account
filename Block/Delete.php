<?php
/**
 * @author Montu Molla <m.molla@lutehc.it>
 */
namespace Lutech\DeleteAccount\Block;

class Delete extends \Magento\Framework\View\Element\Template{
    
    const URL_PATH = "deleteaccount/account/deletePost";
    
    /**
     * @var \Magento\Framework\Data\Form\FormKey 
     */
    protected $formkey;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formkey
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formkey,
        array $data = array()
    ){
        $this->formkey = $formkey;
        parent::__construct($context, $data);
    }
    
    /**
     * Get Form Key
     * @return string
     */
    public function getFormKey(){
        return $this->formkey->getFormKey();
    }
    
    /**
     * Get Form action url
     * @return string
     */
    public function getFormAction(){
        return $this->getUrl(self::URL_PATH);
    }
    
}