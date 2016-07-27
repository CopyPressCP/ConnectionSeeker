#!/usr/bin/perl
###############################################################################
###
###
###
### IP Changer
###
###
### 
###############################################################################
 
$int=eth0;
 
 
 
 
unless (-e "/tmp/line") { open FF, ">/tmp/line"; print FF "1"; close FF; }
 
unless (-e "/tmp/ip") {
$,="\n";
open FF, "/sbin/ifconfig |"||die;
$n=0;
while (<FF>){
if (m|inet addr\:([^\s]+)|) { ($1=~/127\.0\.0\.1/) ? next : ($ips[$n++]=$1);}
}
close FF;
open FF, ">/tmp/ip"||die;
print FF @ips;
print FF "\n";
close FF;
undef $,;
}
$n=qx!cat /tmp/ip|wc -l!;
chomp $n;
unpack "i", $n;
 
open FF, "< /tmp/line";
$i = <FF>;
close FF;
open FF, "< /tmp/ip";
pack "i",$i;
for $j (1..$i) { $ip=<FF>; }
print "/sbin/iptables -t nat -D POSTROUTING 1; /sbin/iptables -t nat -I POSTROUTING -o $int -j SNAT --to-source $ip";
system "/sbin/iptables -t nat -D POSTROUTING 1"; 
system "/sbin/iptables -t nat -I POSTROUTING -o $int -j SNAT --to-source $ip";
open FF, ">/tmp/line";
$i++;
$i=1 if $i>$n;
unpack "i", $i;
print FF $i;
close FF;
exit (0);


