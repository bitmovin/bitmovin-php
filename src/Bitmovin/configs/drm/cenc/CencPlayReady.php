<?php

namespace Bitmovin\configs\drm\cenc;

class CencPlayReady
{
    /**
     * @var string
     */
    private $laUrl;

    /**
     * CencWidevine constructor.
     * @param string $laUrl
     */
    public function __construct($laUrl)
    {
        $this->laUrl = $laUrl;
    }

    /**
     * @return string
     */
    public function getLaUrl()
    {
        return $this->laUrl;
    }

    /**
     * @param string $laUrl
     */
    public function setLaUrl($laUrl)
    {
        $this->laUrl = $laUrl;
    }
    
+    /**
+     * @return string
+     */
+    public function getPssh()
+    {
+        return $this->pssh;
+    }
+    
+    /**
+     * @param string $pssh
+     */
+    public function setPssh($pssh)
+    {
+        $this->pssh = $pssh;
+    }
}
