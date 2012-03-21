<?php

namespace CoreApi\Service\User;

use Core\Entity\User;
use Core\Service\ServiceBase;

use Core\Acl\DefaultAcl;

class UserService 
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\User\UserService
	 * @Inject CoreApi\Service\User\UserService
	 */
	protected $userService;
	
	/**
	 * @var CoreApi\Service\Camp\CampService
	 * @Inject CoreApi\Service\Camp\CampService
	 */
	protected $campService;
	
	/**
	 * Setup ACL
	 * @return void
	 */
	protected function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'createCamp');
	}
	
	/**
	 * Returns the User with the given Identifier
	 * (Identifier can be a MailAddress, a Username or a ID)
	 * 
	 * If no Identifier is given, the Authenticated User is returned
	 * 
	 * @return \Core\Entity\User
	 */
	public function Get($id = null)
	{
		$this->blockIfInvalid(parent::Get($id));
		
		
		/** @var \Core\Entity\Login $user */
		$user = null;
		
		if(isset($id))
		{	return $this->getByIdentifier($id);	}
		
		if(\Zend_Auth::getInstance()->hasIdentity())
		{
			$userId = \Zend_Auth::getInstance()->getIdentity();
			$user = $this->userRepo->find($userId);
		}
		
		return $user;
	}
	
	
	/**
	 * Creates a new User with $username
	 * 
	 * @param string $username
	 * 
	 * @return \Core\Entity\User
	 */
	public function Create(\Zend_Form $form)
	{	
		$t = $this->beginTransaction();
		
		$email = $form->getValue('email');
		$user = $this->userRepo->findOneBy(array('email' => $email));
		
		if(is_null($user))
		{
			$user = new \Core\Entity\User();
			$user->setEmail($email);
			
			$this->persist($user);
		}
			
		if($user->getState() != \Core\Entity\User::STATE_NONREGISTERED)
		{		
			$form->getElement('email')->addError("This eMail-Adress is already registered!");
			$this->validationFailed();
		}
		
		$userValidator = new \Core\Validator\Entity\UserValidator($user);
		$this->validationFailed( ! $userValidator->applyIfValid($form) );	
		
		$user->setState(User::STATE_REGISTERED);
		
		$t->flushAndCommit($s);
			
		return $user;
	}
	
	
	public function Update(\Zend_Form $form)
	{
		/* probably better goes to ACL later, just copied for now from validator */
		$this->validationFailed( $this->userService->get()->getId() != $form->getValue('id') );
		
		// update user
	}
	
	public function Delete(\Zend_Form $form)
	{
		/* probably better goes to ACL later, just copied for now from validator */
		$this->validationFailed( $this->userService->get()->getId() != $form->getValue('id') );
		
		// delete user
	}
    
	
	/**
	 * Activate a User
	 * 
	 * @param \Core\Entity\User|int|string $user
	 * @param string $key
	 * 
	 * @return bool
	 */
	public function Activate($user, $key)
	{
		$user = $this->get($user);
		
		if(is_null($user))
		{
			$this->validationFailed();
			$this->addValidationMessage("User not found!");
		}
		
		if($user->getState() != \Core\Entity\User::STATE_REGISTERED)
		{
			$this->validationFailed();
			$this->addValidationMessage("User already activated!");
		}
		
		return $this->get($user)->activateUser($key);
	}
	
	
	/**
	 * Creates a new Camp
	 * This method is protected, means it is only available from outside (magic!) if ACL is set properly
	 *
	 * @param \Entity\Group $group Owner of the new Camp
	 * @param \Entity\User $user Creator of the new Camp
	 * @param Array $params
	 * @return Camp object, if creation was successfull
	 * @throws \Ecamp\ValidationException
	 */
	public function CreateCamp(\Zend_Form $form, $s = false)
	{
		$t = $this->beginTransaction();
		
		/* check if camp with same name already exists */
		$qb = $this->em->createQueryBuilder();
		$qb->add('select', 'c')
		->add('from', '\Core\Entity\Camp c')
		->add('where', 'c.owner = ?1 AND c.name = ?2')
		->setParameter(1,$this->context->getMe()->getId())
		->setParameter(2, $form->getValue('name'));
		
		$query = $qb->getQuery();
		
		if( count($query->getArrayResult()) > 0 ){
			$form->getElement('name')->addError("Camp with same name already exists.");
			$this->validationFailed();
		}

		/* create camp */
		$camp = $this->campService->Create($form, $s);
		$camp = $this->UnwrapEntity($camp);
		$camp->setOwner($this->context->getMe());
			
		$t->flushAndCommit($s);
		
		return $camp;
	}
	
	
	/**
	* Returns the User for a MailAddress or a Username
	*
	* @param string $identifier
	*
	* @return \Core\Entity\User
	*/
	private function getByIdentifier($identifier)
	{
		$user = null;
		
		$mailValidator = new \Zend_Validate_EmailAddress();
		
		if($identifier instanceOf \Core\Entity\User)
		{
			$user = $identifier;
		}
		elseif($mailValidator->isValid($identifier))
		{
			$user = $this->userRepo->findOneBy(array('email' => $identifier));
		}
		elseif(is_numeric($identifier))
		{
			$user = $this->userRepo->find($identifier);
		}
		else
		{
			$user = $this->userRepo->findOneBy(array('username' => $identifier));
		}
		
		if(is_null($user))
		{
			throw new \Exception("No user found for Identifier: " . $identifier);
		}
	
		return $user;		
	}
}