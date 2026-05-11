--
-- PostgreSQL database dump
--

\restrict TAodixQKUac8D2vemzd0xLCm5HwutlJUp2FbkA8PUhp3nBnxwbJXXrl6bGknhei

-- Dumped from database version 18.3 (Debian 18.3-1.pgdg13+1)
-- Dumped by pg_dump version 18.1

-- Started on 2026-03-17 15:02:18 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 219 (class 1259 OID 16384)
-- Name: voitures; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.voitures (
    immatriculation integer NOT NULL,
    prix integer NOT NULL,
    puissance integer NOT NULL
);


ALTER TABLE public.voitures OWNER TO postgres;

--
-- TOC entry 3437 (class 0 OID 16384)
-- Dependencies: 219
-- Data for Name: voitures; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.voitures (immatriculation, prix, puissance) VALUES (123456789, 20000, 150);
INSERT INTO public.voitures (immatriculation, prix, puissance) VALUES (987654321, 1000, 1000);


--
-- TOC entry 3289 (class 2606 OID 16391)
-- Name: voitures voitures_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.voitures
    ADD CONSTRAINT voitures_pkey PRIMARY KEY (immatriculation);


-- Completed on 2026-03-17 15:02:18 UTC

--
-- PostgreSQL database dump complete
--

\unrestrict TAodixQKUac8D2vemzd0xLCm5HwutlJUp2FbkA8PUhp3nBnxwbJXXrl6bGknhei

