<?php
class Page
{
  //��������
  var $sql;
  var $page;
  var $pageSize;
  var $pageStr;
  //ͳ������
  var $pageCount; //ҳ��
  var $rowCount; //��¼��
  //�������
  var $list = array(); //���������
  var $listSize ;
  //���캯��
  function Page($conn,$sql_in,$page_in,$pageSize_in,$pageStr_in)
  {
    $this->sql = $sql_in;
    $this->page = intval($page_in);
    $this->pageSize = $pageSize_in;
    $this->pageStr = $pageStr_in;
    //ҳ��Ϊ�ջ�С��1�Ĵ���
    if(!$this->page||$this->page<1)
    {
      $this->page = 1;
    }
    //��ѯ�ܼ�¼��
    $rowCountSql = preg_replace("/([\w\W]*?select)([\w\W]*?)(from[\w\W]*?)/i","$1 count(0) $3",$this->sql);
    if(!$conn)
      $rs = mysql_query($rowCountSql) or die("bnc.page: error on getting rowCount.");
    else
      $rs = mysql_query($rowCountSql,$conn) or die("bnc.page: error on getting rowCounts.");
    $rowCountRow = mysql_fetch_row($rs);
    $this->rowCount=$rowCountRow[0];
    //������ҳ��
    if($this->rowCount%$this->pageSize==0)
      $this->pageCount = intval($this->rowCount/$this->pageSize);
    else
      $this->pageCount = intval($this->rowCount/$this->pageSize)+1;
    //SQLƫ����
    $offset = ($this->page-1)*$this->pageSize;
    if(!$conn)
   $rs = mysql_query($this->sql." limit $offset,".$this->pageSize) or
   die("bnc.page: error on listing.");
    else
 $rs = mysql_query($this->sql." limit $offset,".$this->pageSize,$conn) or
 die("bnc.page: error on listing.");
    while($row=mysql_fetch_array($rs))
    {
      $this->list[]=$row;
    }
    $this->listSize = count($this->list);
  }
  /*
   * getPageList��������һ���ϼ򵥵�ҳ���б�
   * �����Ҫ����ҳ���б�,�����޸�����Ĵ���,����ʹ����ҳ��/�ܼ�¼������Ϣ���м�������.
   */
  function getPageList()
  {
    $firstPage;
    $previousPage;
    $pageList;
    $nextPage;
    $lastPage;
    $currentPage;
    //���ҳ��>1����ʾ��ҳ����
    if($this->page>1)
    {
      $firstPage = "<a href=\"".$this->pageStr."1\">��ҳ</a>";
    }
    //���ҳ��>1����ʾ��һҳ����
    if($this->page>1)
    {
      $previousPage = "<a href=\"".$this->pageStr.($this->page-1)."\">��һҳ</a>";
    }
    //���û��βҳ����ʾ��һҳ����
    if($this->page<$this->pageCount)
    {
      $nextPage = "<a href=\"".$this->pageStr.($this->page+1)."\">��һҳ</a>";
    }
    //���û��βҳ����ʾβҳ����
    if($this->page<$this->pageCount)
    {
      $lastPage = "<a href=\"".$this->pageStr.$this->pageCount."\">βҳ</a>";
    }
    //����ҳ���б�
    for($counter=1;$counter<=$this->pageCount;$counter++)
    {
      if($this->page == $counter)
      {
        $currentPage = "<b>".$counter."</b>";
      }
      else
      {
        $currentPage = " "."<a href=\"".$this->pageStr.$counter."\">".$counter."</a>"." ";
      }
      $pageList .= $currentPage;
    }
    return $firstPage." ".$previousPage." ".$pageList." ".$nextPage." ".$lastPage." ";
  }
}
?>