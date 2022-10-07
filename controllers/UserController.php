<?php
class UserController extends Controller
{
  public function Action()
  {
    return $this->render(array(
      'name' => '',
      'password' => '',
      '_token' => $this->generateCsrfToken('account/signup'),
    ));
  }
}
