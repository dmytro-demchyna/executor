DROP DATABASE IF EXISTS schema_keeper;
CREATE DATABASE schema_keeper;

\connect schema_keeper

CREATE OR REPLACE FUNCTION public.test_function(_param integer)
   RETURNS integer
   LANGUAGE plpgsql
AS $function$
DECLARE
BEGIN
   RETURN _param * 2;
END;
$function$;

CREATE OR REPLACE FUNCTION public.test_function2(_param boolean)
    RETURNS boolean
    LANGUAGE plpgsql
AS $function$
DECLARE
BEGIN
    RETURN NOT _param;
END;
$function$;