<?php

namespace ${ModuleName}Test\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ${CtrlName}ControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
            include dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/config/application.config.php'
        );
        $this->entityMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/${ctrlName}');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('${ModuleName}');
        $this->assertControllerName('${ModuleName}\Controller\${CtrlName}');
        $this->assertControllerClass('${CtrlName}Controller');
        $this->assertMatchedRouteName('${ctrlName}');
    }

    public function testAddActionCanBeAccessed()
    {
        $this->dispatch('/${ctrlName}/add');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('${ModuleName}');
        $this->assertControllerName('${ModuleName}\Controller\${CtrlName}');
        $this->assertControllerClass('${CtrlName}Controller');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('${ctrlName}');
    }

    public function testAddActionCanInsertNewData()
    {
        $entityMock = $this->entityMock;

        $entityMock->expects($this->once())
            ->method('persist')
            ->will($this->returnValue(null));
            // ->with($this->attributeEqualTo('descricao', 'Led Zeppelin III'));
        $entityMock->expects($this->once())
            ->method('flush')
            ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Doctrine\ORM\EntityManager', $entityMock);

        $postData = array(${arrayData});
        $this->dispatch('/${ctrlName}/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/${ctrlName}/');
    }
    
    public function testAddActionCannotInsertInvalidData()
    {
        $post = array(${arrayData1});
        $this->dispatch('/${ctrlName}/add', 'POST', $post);

        $this->assertQueryContentContains('form ul li', "Value is required and can't be empty");
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('${ModuleName}');
        $this->assertControllerName('${ModuleName}\Controller\${CtrlName}');
        $this->assertControllerClass('${CtrlName}Controller');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('${ctrlName}');
    }

    public function testEditActionRedirectWithoutId()
    {
        $this->dispatch('/${ctrlName}/edit/0', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/${ctrlName}/add');
    }

    public function testEditActionShowsData()
    {

        $${CtrlName} = new \${ModuleName}\Entity\${CtrlName}();
        $${CtrlName}->populate(array(${arrayData2}));

        $entityMock = $this->entityMock;
        $entityMock->expects($this->once())
            ->method('find')
            ->with('${ModuleName}\Entity\${CtrlName}', 7)
            // ->will($this->returnValue(null));
            ->will($this->returnValue($${CtrlName}));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Doctrine\ORM\EntityManager', $entityMock);

        $this->dispatch('/${ctrlName}/edit/7', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('${ModuleName}');
        $this->assertControllerName('${ModuleName}\Controller\${CtrlName}');
        $this->assertControllerClass('${CtrlName}Controller');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('${ctrlName}');

        $this->assertContains('7', $this->getResponse()->getContent());

    }

    public function testViewActionRedirectWithoutId()
    {
        $this->dispatch('/${ctrlName}/view/0', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/${ctrlName}/');
    }

    public function testViewActionCanBeAccessed()
    {

        $${CtrlName} = new \${ModuleName}\Entity\${CtrlName}();
        $${CtrlName}->populate(array(${arrayData2}));

        $entityMock = $this->entityMock;

        $entityMock->expects($this->once())
            ->method('find')
            ->with('${ModuleName}\Entity\${CtrlName}', 7)
            // ->will($this->returnValue(null));
            ->will($this->returnValue($${CtrlName}));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Doctrine\ORM\EntityManager', $entityMock);

        $this->dispatch('/${ctrlName}/view/7', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('${ModuleName}');
        $this->assertControllerName('${ModuleName}\Controller\${CtrlName}');
        $this->assertControllerClass('${CtrlName}Controller');
        $this->assertActionName('view');
        $this->assertMatchedRouteName('${ctrlName}');

        $this->assertContains('7', $this->getResponse()->getContent());

    }

    public function testDeleteActionRedirectWithoutId()
    {
        $this->dispatch('/${ctrlName}/delete/0', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/${ctrlName}/');
    }

    public function testDeleteActionCanRemoveData()
    {
        $${CtrlName} = new \${ModuleName}\Entity\${CtrlName}();
        $${CtrlName}->populate(array(${arrayData2}));

        $entityMock = $this->entityMock;

        $entityMock->expects($this->once())
            ->method('find')
            ->with('${ModuleName}\Entity\${CtrlName}', 7)
            ->will($this->returnValue($${CtrlName}));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Doctrine\ORM\EntityManager', $entityMock);

        $this->dispatch('/${ctrlName}/delete/7', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('${ModuleName}');
        $this->assertControllerName('${ModuleName}\Controller\${CtrlName}');
        $this->assertControllerClass('${CtrlName}Controller');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('${ctrlName}');

    }
}