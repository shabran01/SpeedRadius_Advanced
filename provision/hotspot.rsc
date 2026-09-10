############################################
# CLEANUP (safe to run on fresh or partial config)
############################################
/interface bridge port remove [find interface=ether2]
/interface bridge port remove [find interface=ether3]
/interface bridge remove [find name=Hotspot-Server]
/ip hotspot remove [find name=hotspot1]
/ip hotspot profile remove [find name=hsprof1]
/ip dhcp-server remove [find name=dhcp1]
/ip pool remove [find name=hotspot-pool]
/ip address remove [find address="10.0.0.1/22"]

############################################
# 1. ADMIN USER (DO NOT DELETE)
############################################
/user add name=speedradius password="KWEYU01@@" group=full comment="DONT DELETE"

############################################
# 2. BRIDGE (LAN NETWORK)
############################################
/interface bridge add name=Hotspot-Server
/interface bridge port add bridge=Hotspot-Server interface=ether2
/interface bridge port add bridge=Hotspot-Server interface=ether3

############################################
# 3. IP ADDRESSING (/22 SUBNET)
############################################
/ip address add address=10.0.0.1/22 interface=Hotspot-Server

############################################
# 4. DHCP SERVER
############################################
/ip pool add name=hotspot-pool ranges=10.0.0.2-10.0.3.254
/ip dhcp-server add name=dhcp1 interface=Hotspot-Server address-pool=hotspot-pool disabled=no
/ip dhcp-server network add address=10.0.0.0/22 gateway=10.0.0.1 dns-server=8.8.8.8,1.1.1.1

############################################
# 5. DNS SETTINGS
############################################
/ip dns set allow-remote-requests=yes servers=8.8.8.8,1.1.1.1

############################################
# 6. INTERNET NAT (ISP ON ETHER1)
############################################
/ip firewall nat add chain=srcnat out-interface=ether1 action=masquerade

############################################
# 7. HOTSPOT PROFILE (AUTH METHODS)
############################################
/ip hotspot profile add name=hsprof1 hotspot-address=10.0.0.1 dns-name=hotspot.wifi login-by=http-chap,http-pap,https,mac-cookie,mac mac-auth-password=1234 use-radius=no

############################################
# 8. HOTSPOT SERVER
############################################
/ip hotspot add name=hotspot1 interface=Hotspot-Server address-pool=hotspot-pool profile=hsprof1 disabled=no

############################################
# 9. WALLED GARDEN
############################################
/ip hotspot walled-garden add dst-host=hotspot.wifi comment="Hotspot Login"
/ip hotspot walled-garden ip add dst-host=speedcomwifi.co.ke action=accept
/ip hotspot walled-garden ip add dst-host=*.speedcomwifi.co.ke action=accept
/ip hotspot walled-garden ip add dst-host=code.jquery.com action=accept
/ip hotspot walled-garden ip add dst-host=cdn.jsdelivr.net action=accept
/ip hotspot walled-garden ip add dst-host=cdnjs.cloudflare.com action=accept
/ip hotspot walled-garden ip add dst-host=fonts.googleapis.com action=accept
/ip hotspot walled-garden ip add dst-host=cdn.tailwindcss.com action=accept
/ip hotspot walled-garden ip add dst-host=ajax.googleapis.com action=accept

############################################
# 10. HOTSPOT USER
############################################
/ip hotspot user add name=speedradius password=1234 profile=default

############################################
# 11. DISABLE FIREWALL DROP & FASTTRACK RULES
############################################
/ip firewall filter set [find action=drop] disabled=yes
/ip firewall filter set [find action=fasttrack-connection] disabled=yes