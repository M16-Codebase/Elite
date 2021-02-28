<?php
    $error_stage= '
        YTo2OntzOjY6InNlcnZlciI7YTozMjp7czoxNToiUkVESVJFQ1RfU1RBVFVTIjtzOjM6IjIwMCI7czo5OiJIVFRQX0hPU1QiO3M6MjM6Im0xNi1lbGl0ZS53ZWJhY3RpdmVzLnJ1IjtzOjE2OiJIVFRQX1JFTU9URV9BRERSIjtzOjE0OiI3Ny4yMzQuMjIwLjIxNCI7czoyMDoiSFRUUF9YX0ZPUldBUkRFRF9GT1IiO3M6MTQ6Ijc3LjIzNC4yMjAuMjE0IjtzOjE1OiJIVFRQX0NPTk5FQ1RJT04iO3M6NToiY2xvc2UiO3M6MTU6IkhUVFBfVVNFUl9BR0VOVCI7czo3MjoiTW96aWxsYS81LjAgKFdpbmRvd3MgTlQgNi4xOyBXT1c2NDsgcnY6NDEuMCkgR2Vja28vMjAxMDAxMDEgRmlyZWZveC80MS4wIjtzOjExOiJIVFRQX0FDQ0VQVCI7czo2MzoidGV4dC9odG1sLGFwcGxpY2F0aW9uL3hodG1sK3htbCxhcHBsaWNhdGlvbi94bWw7cT0wLjksKi8qO3E9MC44IjtzOjIwOiJIVFRQX0FDQ0VQVF9MQU5HVUFHRSI7czozNToicnUtUlUscnU7cT0wLjgsZW4tVVM7cT0wLjUsZW47cT0wLjMiO3M6MjA6IkhUVFBfQUNDRVBUX0VOQ09ESU5HIjtzOjEzOiJnemlwLCBkZWZsYXRlIjtzOjEyOiJIVFRQX1JFRkVSRVIiO3M6NDY6Imh0dHA6Ly9tMTYtZWxpdGUud2ViYWN0aXZlcy5ydS9lbi9yZWFsX2VzdGF0ZS8iO3M6MTE6IkhUVFBfQ09PS0lFIjtzOjQxOToiX2dhPUdBMS4yLjU2NDgzNTIyMi4xNDM1NjcxOTM5OyBmYXZvcml0ZXM9JTdCJTIycmVzYWxlJTIyJTNBJTdCJTIyZW50aXR5X2lkcyUyMiUzQSU1QiUyMjU4NyUyMiUyQyUyMjYwMiUyMiUyQyUyMjYwMSUyMiU1RCUyQyUyMmRhdGVzJTIyJTNBJTdCJTIyNjAxJTIyJTNBMTQ0NTUyMjA0MSUyQyUyMjYwMiUyMiUzQTE0NDUzNTgzNjglMkMlMjI1ODclMjIlM0ExNDQ1MzQ2MDgwJTdEJTJDJTIyY29tbWVudHMlMjIlM0ElNUIlNUQlN0QlN0Q7IHVzZXJfaWQ9MTQ5OyBwYXNzX2hhc2g9YjBmNjAxMDJlZWVjMDRiZjZiNDcwOTUyNThmMzNmYTI7IGhhc2g9YjMyZDI4MTVlOWU0NDQwNWVmNGE3OWE0NzJkYjdlYTE7IGF1dGg9OGM1ZWI4MmU0ODk1NWRmM2E5NzVkYzM4ZjUxOGIyNGI7IHByaW1hcnk9bDBhMDQ3cmlsa2hwZ3MyaWNqMWRpcnBrZTMiO3M6NDoiUEFUSCI7czoyODoiL3Vzci9sb2NhbC9iaW46L3Vzci9iaW46L2JpbiI7czoxNjoiU0VSVkVSX1NJR05BVFVSRSI7czo4NDoiPGFkZHJlc3M+QXBhY2hlLzIuMi4yMiAoRGViaWFuKSBTZXJ2ZXIgYXQgbTE2LWVsaXRlLndlYmFjdGl2ZXMucnUgUG9ydCA4MDwvYWRkcmVzcz4KIjtzOjE1OiJTRVJWRVJfU09GVFdBUkUiO3M6MjI6IkFwYWNoZS8yLjIuMjIgKERlYmlhbikiO3M6MTE6IlNFUlZFUl9OQU1FIjtzOjIzOiJtMTYtZWxpdGUud2ViYWN0aXZlcy5ydSI7czoxMToiU0VSVkVSX0FERFIiO3M6OToiMTI3LjAuMC4xIjtzOjExOiJTRVJWRVJfUE9SVCI7czoyOiI4MCI7czoxMToiUkVNT1RFX0FERFIiO3M6MTQ6Ijc3LjIzNC4yMjAuMjE0IjtzOjEzOiJET0NVTUVOVF9ST09UIjtzOjQ4OiIvaG9tZS9scHMvZG9tYWlucy9tMTYtZWxpdGUud2ViYWN0aXZlcy5ydS9wdWJsaWMiO3M6MTI6IlNFUlZFUl9BRE1JTiI7czoxODoiW25vIGFkZHJlc3MgZ2l2ZW5dIjtzOjE1OiJTQ1JJUFRfRklMRU5BTUUiO3M6NTg6Ii9ob21lL2xwcy9kb21haW5zL20xNi1lbGl0ZS53ZWJhY3RpdmVzLnJ1L3B1YmxpYy9pbmRleC5waHAiO3M6MTE6IlJFTU9URV9QT1JUIjtzOjU6IjM4ODczIjtzOjEyOiJSRURJUkVDVF9VUkwiO3M6MTE6Ii9lbi9yZXNhbGUvIjtzOjE3OiJHQVRFV0FZX0lOVEVSRkFDRSI7czo3OiJDR0kvMS4xIjtzOjE1OiJTRVJWRVJfUFJPVE9DT0wiO3M6ODoiSFRUUC8xLjAiO3M6MTQ6IlJFUVVFU1RfTUVUSE9EIjtzOjM6IkdFVCI7czoxMjoiUVVFUllfU1RSSU5HIjtzOjA6IiI7czoxMToiUkVRVUVTVF9VUkkiO3M6MTE6Ii9lbi9yZXNhbGUvIjtzOjExOiJTQ1JJUFRfTkFNRSI7czoxMDoiL2luZGV4LnBocCI7czo4OiJQSFBfU0VMRiI7czoxMDoiL2luZGV4LnBocCI7czoxODoiUkVRVUVTVF9USU1FX0ZMT0FUIjtkOjE0NDU1MzM2NzAuMDM2OTk5OTtzOjEyOiJSRVFVRVNUX1RJTUUiO2k6MTQ0NTUzMzY3MDt9czo2OiJjb29raWUiO2E6Nzp7czozOiJfZ2EiO3M6MjY6IkdBMS4yLjU2NDgzNTIyMi4xNDM1NjcxOTM5IjtzOjk6ImZhdm9yaXRlcyI7czoxMjA6InsicmVzYWxlIjp7ImVudGl0eV9pZHMiOlsiNTg3IiwiNjAyIiwiNjAxIl0sImRhdGVzIjp7IjYwMSI6MTQ0NTUyMjA0MSwiNjAyIjoxNDQ1MzU4MzY4LCI1ODciOjE0NDUzNDYwODB9LCJjb21tZW50cyI6W119fSI7czo3OiJ1c2VyX2lkIjtzOjM6IjE0OSI7czo5OiJwYXNzX2hhc2giO3M6MzI6ImIwZjYwMTAyZWVlYzA0YmY2YjQ3MDk1MjU4ZjMzZmEyIjtzOjQ6Imhhc2giO3M6MzI6ImIzMmQyODE1ZTllNDQ0MDVlZjRhNzlhNDcyZGI3ZWExIjtzOjQ6ImF1dGgiO3M6MzI6IjhjNWViODJlNDg5NTVkZjNhOTc1ZGMzOGY1MThiMjRiIjtzOjc6InByaW1hcnkiO3M6MjY6ImwwYTA0N3JpbGtocGdzMmljajFkaXJwa2UzIjt9czo3OiJzZXNzaW9uIjthOjM6e3M6MTU6Il9zZjJfYXR0cmlidXRlcyI7YTowOnt9czoxMjoiX3NmMl9mbGFzaGVzIjthOjA6e31zOjk6Il9zZjJfbWV0YSI7YTozOntzOjE6InUiO2k6MTQ0NTUzMzY3MDtzOjE6ImMiO2k6MTQ0NTUyODgwNjtzOjE6ImwiO3M6MToiMCI7fX1zOjQ6InBvc3QiO2E6MDp7fXM6MzoiZ2V0IjthOjA6e31zOjQ6ImZpbGUiO2E6MDp7fX0=
    ';
    $error_data = unserialize(base64_decode(trim($error_stage)));
    print_r($error_data);