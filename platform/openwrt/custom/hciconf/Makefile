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
	$(INSTALL_DIR) $(1)/www/
	$(CP) ./files/www/* $(1)/www/
	$(INSTALL_DIR) $(1)/etc/init.d
	$(INSTALL_BIN) ./files/hciconf.init $(1)/etc/init.d/hciconf
endef

$(eval $(call BuildPackage,hciconf))
