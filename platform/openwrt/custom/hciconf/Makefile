include $(TOPDIR)/rules.mk

PKG_NAME:=hciconf
PKG_VERSION:=1.0
PKG_RELEASE:=1

include $(INCLUDE_DIR)/package.mk

define Package/hciconf
  SECTION:=net
  CATEGORY:=ECE
  SUBMENU:=ecetools
  TITLE:=HCI Configurator
  DEPENDS:= +nginx +php7 +coreutils-stdbuf
endef

define Package/hciconf/description
This package contains the PHP and other scripts necessary to interface to a
footboard controller and configure it via the web.
endef

define Build/Compile
endef

define Package/hciconf/install
	$(INSTALL_DIR) $(1)/etc
	$(CP) ./files/etc/* $(1)/etc
	$(INSTALL_DIR) $(1)/etc/init.d
	$(INSTALL_BIN) ./files/setbaud.init $(1)/etc/init.d/setbaud
	$(INSTALL_DIR) $(1)/www/hciconf/
	$(CP) ./files/www/hciconf/* $(1)/www/hciconf/
	$(INSTALL_DIR) $(1)/usr/bin
	$(INSTALL_BIN) ./files/wifi_watchdog $(1)/usr/bin/
	$(INSTALL_BIN) ./files/delaywifi $(1)/usr/bin/
	$(INSTALL_DATA) ./files/wifis.awk $(1)/usr/bin/
endef

$(eval $(call BuildPackage,hciconf))
