<?php

class OrgWebUser extends UWebUser {
    
    /**
     * 社团id
     */
    public function getOrgId()
    {
        $orgId = $this->getState('org_id');
        if (empty($orgId)) {
            $org = OrgInfoO::model()->getByUidO($this->getId());
            if (empty($org)) {
                return NULL;
            }
            $orgId = $org->id;
            if (!empty($orgId)) {
                $this->setState('org_id', $orgId);
            }
        }
        return $orgId;
    }
    
    
    /**
     * 社团名称
     */
    public function getOrgName()
    {
        $orgName = $this->getState('org_name');
        if (empty($orgName)) {
            $org = OrgInfoO::model()->getByUidO($this->getId());
            if (empty($org)) {
                return NULL;
            }
            $orgName = $org->name;
            if (!empty($orgName)) {
                $this->setState('org_name', $orgName);
            }
        }
        return $orgName;
    }
    
    
    /**
     * 社团logo图片url
     */
    public function getLogoImgUrl() 
    {
        $logoImgUrl = $this->getState('logo_img_url');
        if (empty($logoImgUrl)) {
            $org = OrgInfoO::model()->getByUidO($this->getId());
            if (empty($org)) {
                return NULL;
            }
            $img = ImgInfo::model()->profile($org->logo_img_id);
            $logoImgUrl = empty($img) ? NULL : $img['img_url'];
            if (!empty($logoImgUrl)) {
                $this->setState('logo_img_url', $logoImgUrl);
            }
        }
        return $logoImgUrl;
    }
    
}

?>