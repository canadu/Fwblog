<?php
class AdminUserController extends Controller
{
  //管理者登録
  public function signupAction()
  {
    return $this->render(array(
      'name' => '',
      'password' => '',
      '_token' => $this->generateCsrfToken('account/signup'),
    ));
  }
}
