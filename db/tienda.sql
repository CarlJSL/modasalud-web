--
-- PostgreSQL database dump
--

-- Dumped from database version 15.13 (Debian 15.13-1.pgdg120+1)
-- Dumped by pg_dump version 15.13

-- Started on 2025-07-03 04:08:30 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 874 (class 1247 OID 16390)
-- Name: order_status; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.order_status AS ENUM (
    'PENDING',
    'COMPLETED',
    'CANCELLED'
);


ALTER TYPE public.order_status OWNER TO admin;

--
-- TOC entry 877 (class 1247 OID 16398)
-- Name: payment_method; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.payment_method AS ENUM (
    'YAPE',
    'PLIN',
    'TRANSFER',
    'CASH'
);


ALTER TYPE public.payment_method OWNER TO admin;

--
-- TOC entry 880 (class 1247 OID 16408)
-- Name: payment_status; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.payment_status AS ENUM (
    'PAID',
    'PENDING',
    'FAILED'
);


ALTER TYPE public.payment_status OWNER TO admin;

--
-- TOC entry 952 (class 1247 OID 25060)
-- Name: payment_status_enum; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.payment_status_enum AS ENUM (
    'PENDING',
    'PAID',
    'FAILED',
    'REFUNDED'
);


ALTER TYPE public.payment_status_enum OWNER TO admin;

--
-- TOC entry 883 (class 1247 OID 16416)
-- Name: product_size; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.product_size AS ENUM (
    'XS',
    'S',
    'M',
    'L',
    'XL',
    'XXL',
    'UNIQUE'
);


ALTER TYPE public.product_size OWNER TO admin;

--
-- TOC entry 946 (class 1247 OID 25032)
-- Name: product_status; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.product_status AS ENUM (
    'ACTIVE',
    'INACTIVE',
    'DISCONTINUED',
    'OUT_OF_STOCK',
    'COMING_SOON',
    'ON_SALE'
);


ALTER TYPE public.product_status OWNER TO admin;

--
-- TOC entry 886 (class 1247 OID 16432)
-- Name: role; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.role AS ENUM (
    'ADMIN',
    'SELLER',
    'CUSTOMER'
);


ALTER TYPE public.role OWNER TO admin;

--
-- TOC entry 889 (class 1247 OID 16440)
-- Name: user_status; Type: TYPE; Schema: public; Owner: admin
--

CREATE TYPE public.user_status AS ENUM (
    'ACTIVE',
    'INACTIVE'
);


ALTER TYPE public.user_status OWNER TO admin;

--
-- TOC entry 251 (class 1255 OID 25046)
-- Name: update_product_status(); Type: FUNCTION; Schema: public; Owner: admin
--

CREATE FUNCTION public.update_product_status() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Si el stock llega a 0, cambiar a OUT_OF_STOCK (solo si estaba ACTIVE)
    IF NEW.stock = 0 AND OLD.stock > 0 AND OLD.status = 'ACTIVE' THEN
        NEW.status = 'OUT_OF_STOCK';
    END IF;
    
    -- Si el stock se restaura y estaba OUT_OF_STOCK, cambiar a ACTIVE
    IF NEW.stock > 0 AND OLD.stock = 0 AND OLD.status = 'OUT_OF_STOCK' THEN
        NEW.status = 'ACTIVE';
    END IF;
    
    -- Si se marca deleted_at, cambiar a DISCONTINUED
    IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
        NEW.status = 'DISCONTINUED';
    END IF;
    
    -- Si se quita deleted_at, cambiar a ACTIVE (si tiene stock) o OUT_OF_STOCK
    IF NEW.deleted_at IS NULL AND OLD.deleted_at IS NOT NULL THEN
        IF NEW.stock > 0 THEN
            NEW.status = 'ACTIVE';
        ELSE
            NEW.status = 'OUT_OF_STOCK';
        END IF;
    END IF;
    
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_product_status() OWNER TO admin;

--
-- TOC entry 250 (class 1255 OID 16445)
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: admin
--

CREATE FUNCTION public.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.updated_at = NOW();
   RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_updated_at_column() OWNER TO admin;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 214 (class 1259 OID 16446)
-- Name: audit_log; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.audit_log (
    id bigint NOT NULL,
    table_name text NOT NULL,
    record_id text NOT NULL,
    action text NOT NULL,
    changed_fields json,
    old_values json,
    new_values json,
    performed_by text NOT NULL,
    ip_address text,
    user_agent text,
    performed_at timestamp with time zone DEFAULT now(),
    CONSTRAINT audit_log_action_check CHECK ((action = ANY (ARRAY['CREATE'::text, 'UPDATE'::text, 'DELETE'::text])))
);


ALTER TABLE public.audit_log OWNER TO admin;

--
-- TOC entry 215 (class 1259 OID 16453)
-- Name: audit_log_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.audit_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.audit_log_id_seq OWNER TO admin;

--
-- TOC entry 3630 (class 0 OID 0)
-- Dependencies: 215
-- Name: audit_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.audit_log_id_seq OWNED BY public.audit_log.id;


--
-- TOC entry 216 (class 1259 OID 16454)
-- Name: cart_items; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.cart_items (
    id bigint NOT NULL,
    product_id bigint,
    quantity integer DEFAULT 1 NOT NULL,
    added_at timestamp with time zone DEFAULT now(),
    client_id bigint
);


ALTER TABLE public.cart_items OWNER TO admin;

--
-- TOC entry 217 (class 1259 OID 16459)
-- Name: cart_items_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.cart_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cart_items_id_seq OWNER TO admin;

--
-- TOC entry 3631 (class 0 OID 0)
-- Dependencies: 217
-- Name: cart_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.cart_items_id_seq OWNED BY public.cart_items.id;


--
-- TOC entry 218 (class 1259 OID 16460)
-- Name: categories; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name text NOT NULL,
    description text
);


ALTER TABLE public.categories OWNER TO admin;

--
-- TOC entry 219 (class 1259 OID 16465)
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.categories_id_seq OWNER TO admin;

--
-- TOC entry 3632 (class 0 OID 0)
-- Dependencies: 219
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- TOC entry 245 (class 1259 OID 16744)
-- Name: client_addresses; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.client_addresses (
    id bigint NOT NULL,
    client_id bigint,
    address text NOT NULL,
    city text,
    region text,
    postal_code text,
    phone text,
    is_default boolean DEFAULT false,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.client_addresses OWNER TO admin;

--
-- TOC entry 244 (class 1259 OID 16743)
-- Name: client_addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.client_addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.client_addresses_id_seq OWNER TO admin;

--
-- TOC entry 3633 (class 0 OID 0)
-- Dependencies: 244
-- Name: client_addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.client_addresses_id_seq OWNED BY public.client_addresses.id;


--
-- TOC entry 243 (class 1259 OID 16705)
-- Name: clients; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.clients (
    id bigint NOT NULL,
    name text NOT NULL,
    email text NOT NULL,
    phone text,
    dni text,
    gender text,
    birth_date date,
    status public.user_status DEFAULT 'ACTIVE'::public.user_status,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.clients OWNER TO admin;

--
-- TOC entry 242 (class 1259 OID 16704)
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clients_id_seq OWNER TO admin;

--
-- TOC entry 3634 (class 0 OID 0)
-- Dependencies: 242
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.clients_id_seq OWNED BY public.clients.id;


--
-- TOC entry 239 (class 1259 OID 16668)
-- Name: coupons; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.coupons (
    id bigint NOT NULL,
    code text NOT NULL,
    description text,
    discount_type text NOT NULL,
    discount_value numeric(10,2) NOT NULL,
    max_uses integer DEFAULT 1,
    used_count integer DEFAULT 0,
    expires_at timestamp with time zone,
    created_at timestamp with time zone DEFAULT now(),
    is_active boolean DEFAULT true,
    CONSTRAINT coupons_discount_type_check CHECK ((discount_type = ANY (ARRAY['PERCENTAGE'::text, 'FIXED'::text])))
);


ALTER TABLE public.coupons OWNER TO admin;

--
-- TOC entry 238 (class 1259 OID 16667)
-- Name: coupons_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.coupons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.coupons_id_seq OWNER TO admin;

--
-- TOC entry 3635 (class 0 OID 0)
-- Dependencies: 238
-- Name: coupons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.coupons_id_seq OWNED BY public.coupons.id;


--
-- TOC entry 220 (class 1259 OID 16466)
-- Name: order_items; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.order_items (
    order_id bigint NOT NULL,
    product_id bigint NOT NULL,
    quantity integer NOT NULL,
    price numeric(10,2) NOT NULL
);


ALTER TABLE public.order_items OWNER TO admin;

--
-- TOC entry 221 (class 1259 OID 16469)
-- Name: orders; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.orders (
    id bigint NOT NULL,
    address_id bigint,
    total_price numeric(10,2) NOT NULL,
    status public.order_status DEFAULT 'PENDING'::public.order_status,
    created_at timestamp with time zone DEFAULT now(),
    coupon_id bigint,
    discount_amount numeric(10,2) DEFAULT 0,
    client_id bigint
);


ALTER TABLE public.orders OWNER TO admin;

--
-- TOC entry 222 (class 1259 OID 16474)
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.orders_id_seq OWNER TO admin;

--
-- TOC entry 3636 (class 0 OID 0)
-- Dependencies: 222
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- TOC entry 223 (class 1259 OID 16475)
-- Name: payments; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.payments (
    id bigint NOT NULL,
    order_id bigint,
    method public.payment_method NOT NULL,
    status public.payment_status DEFAULT 'PENDING'::public.payment_status,
    paid_at timestamp with time zone,
    proof_url text
);


ALTER TABLE public.payments OWNER TO admin;

--
-- TOC entry 224 (class 1259 OID 16481)
-- Name: payments_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.payments_id_seq OWNER TO admin;

--
-- TOC entry 3637 (class 0 OID 0)
-- Dependencies: 224
-- Name: payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.payments_id_seq OWNED BY public.payments.id;


--
-- TOC entry 225 (class 1259 OID 16482)
-- Name: permissions; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    role_id bigint,
    table_name text NOT NULL,
    can_create boolean DEFAULT false,
    can_read boolean DEFAULT false,
    can_update boolean DEFAULT false,
    can_delete boolean DEFAULT false
);


ALTER TABLE public.permissions OWNER TO admin;

--
-- TOC entry 226 (class 1259 OID 16491)
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permissions_id_seq OWNER TO admin;

--
-- TOC entry 3638 (class 0 OID 0)
-- Dependencies: 226
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- TOC entry 227 (class 1259 OID 16492)
-- Name: product_categories; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.product_categories (
    id bigint NOT NULL,
    name text,
    description text,
    created_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.product_categories OWNER TO admin;

--
-- TOC entry 246 (class 1259 OID 24997)
-- Name: product_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.product_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.product_categories_id_seq OWNER TO admin;

--
-- TOC entry 3639 (class 0 OID 0)
-- Dependencies: 246
-- Name: product_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.product_categories_id_seq OWNED BY public.product_categories.id;


--
-- TOC entry 247 (class 1259 OID 25006)
-- Name: product_category_mapping; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.product_category_mapping (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    category_id bigint NOT NULL,
    product_category_id bigint NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.product_category_mapping OWNER TO admin;

--
-- TOC entry 248 (class 1259 OID 25014)
-- Name: product_category_mapping_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.product_category_mapping_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.product_category_mapping_id_seq OWNER TO admin;

--
-- TOC entry 3640 (class 0 OID 0)
-- Dependencies: 248
-- Name: product_category_mapping_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.product_category_mapping_id_seq OWNED BY public.product_category_mapping.id;


--
-- TOC entry 228 (class 1259 OID 16495)
-- Name: product_images; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.product_images (
    id bigint NOT NULL,
    product_id bigint,
    image_url text NOT NULL,
    created_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.product_images OWNER TO admin;

--
-- TOC entry 229 (class 1259 OID 16501)
-- Name: product_images_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.product_images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.product_images_id_seq OWNER TO admin;

--
-- TOC entry 3641 (class 0 OID 0)
-- Dependencies: 229
-- Name: product_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.product_images_id_seq OWNED BY public.product_images.id;


--
-- TOC entry 230 (class 1259 OID 16502)
-- Name: product_reviews; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.product_reviews (
    id bigint NOT NULL,
    product_id bigint,
    rating integer,
    comment text,
    created_at timestamp with time zone DEFAULT now(),
    client_id bigint,
    CONSTRAINT product_reviews_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.product_reviews OWNER TO admin;

--
-- TOC entry 231 (class 1259 OID 16509)
-- Name: product_reviews_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.product_reviews_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.product_reviews_id_seq OWNER TO admin;

--
-- TOC entry 3642 (class 0 OID 0)
-- Dependencies: 231
-- Name: product_reviews_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.product_reviews_id_seq OWNED BY public.product_reviews.id;


--
-- TOC entry 232 (class 1259 OID 16510)
-- Name: products; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.products (
    id bigint NOT NULL,
    name text NOT NULL,
    description text,
    price numeric(10,2) NOT NULL,
    stock integer NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    size public.product_size DEFAULT 'UNIQUE'::public.product_size,
    product_type_id bigint,
    status public.product_status DEFAULT 'ACTIVE'::public.product_status,
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.products OWNER TO admin;

--
-- TOC entry 249 (class 1259 OID 25053)
-- Name: products_available; Type: VIEW; Schema: public; Owner: admin
--

CREATE VIEW public.products_available AS
 SELECT products.id,
    products.name,
    products.description,
    products.price,
    products.stock,
    products.size,
    products.status
   FROM public.products
  WHERE ((products.deleted_at IS NULL) AND (products.stock > 0));


ALTER TABLE public.products_available OWNER TO admin;

--
-- TOC entry 233 (class 1259 OID 16517)
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.products_id_seq OWNER TO admin;

--
-- TOC entry 3643 (class 0 OID 0)
-- Dependencies: 233
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- TOC entry 241 (class 1259 OID 16690)
-- Name: review_images; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.review_images (
    id bigint NOT NULL,
    review_id bigint NOT NULL,
    image_url text NOT NULL,
    uploaded_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.review_images OWNER TO admin;

--
-- TOC entry 240 (class 1259 OID 16689)
-- Name: review_images_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.review_images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.review_images_id_seq OWNER TO admin;

--
-- TOC entry 3644 (class 0 OID 0)
-- Dependencies: 240
-- Name: review_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.review_images_id_seq OWNED BY public.review_images.id;


--
-- TOC entry 234 (class 1259 OID 16518)
-- Name: roles; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name text NOT NULL,
    description text
);


ALTER TABLE public.roles OWNER TO admin;

--
-- TOC entry 235 (class 1259 OID 16523)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO admin;

--
-- TOC entry 3645 (class 0 OID 0)
-- Dependencies: 235
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 236 (class 1259 OID 16531)
-- Name: users; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    username text NOT NULL,
    password text NOT NULL,
    email text NOT NULL,
    name text,
    status public.user_status DEFAULT 'ACTIVE'::public.user_status,
    created_by text,
    updated_by text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    role_id bigint
);


ALTER TABLE public.users OWNER TO admin;

--
-- TOC entry 237 (class 1259 OID 16540)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO admin;

--
-- TOC entry 3646 (class 0 OID 0)
-- Dependencies: 237
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3313 (class 2604 OID 16541)
-- Name: audit_log id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.audit_log ALTER COLUMN id SET DEFAULT nextval('public.audit_log_id_seq'::regclass);


--
-- TOC entry 3315 (class 2604 OID 16542)
-- Name: cart_items id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cart_items ALTER COLUMN id SET DEFAULT nextval('public.cart_items_id_seq'::regclass);


--
-- TOC entry 3318 (class 2604 OID 16543)
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- TOC entry 3357 (class 2604 OID 16747)
-- Name: client_addresses id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.client_addresses ALTER COLUMN id SET DEFAULT nextval('public.client_addresses_id_seq'::regclass);


--
-- TOC entry 3353 (class 2604 OID 16708)
-- Name: clients id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clients ALTER COLUMN id SET DEFAULT nextval('public.clients_id_seq'::regclass);


--
-- TOC entry 3346 (class 2604 OID 16671)
-- Name: coupons id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.coupons ALTER COLUMN id SET DEFAULT nextval('public.coupons_id_seq'::regclass);


--
-- TOC entry 3319 (class 2604 OID 16544)
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- TOC entry 3323 (class 2604 OID 16545)
-- Name: payments id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payments ALTER COLUMN id SET DEFAULT nextval('public.payments_id_seq'::regclass);


--
-- TOC entry 3325 (class 2604 OID 16546)
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- TOC entry 3330 (class 2604 OID 24998)
-- Name: product_categories id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_categories ALTER COLUMN id SET DEFAULT nextval('public.product_categories_id_seq'::regclass);


--
-- TOC entry 3361 (class 2604 OID 25015)
-- Name: product_category_mapping id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_category_mapping ALTER COLUMN id SET DEFAULT nextval('public.product_category_mapping_id_seq'::regclass);


--
-- TOC entry 3332 (class 2604 OID 16547)
-- Name: product_images id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_images ALTER COLUMN id SET DEFAULT nextval('public.product_images_id_seq'::regclass);


--
-- TOC entry 3334 (class 2604 OID 16548)
-- Name: product_reviews id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_reviews ALTER COLUMN id SET DEFAULT nextval('public.product_reviews_id_seq'::regclass);


--
-- TOC entry 3336 (class 2604 OID 16549)
-- Name: products id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- TOC entry 3351 (class 2604 OID 16693)
-- Name: review_images id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.review_images ALTER COLUMN id SET DEFAULT nextval('public.review_images_id_seq'::regclass);


--
-- TOC entry 3341 (class 2604 OID 16550)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 3342 (class 2604 OID 16552)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3590 (class 0 OID 16446)
-- Dependencies: 214
-- Data for Name: audit_log; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.audit_log (id, table_name, record_id, action, changed_fields, old_values, new_values, performed_by, ip_address, user_agent, performed_at) FROM stdin;
\.


--
-- TOC entry 3592 (class 0 OID 16454)
-- Dependencies: 216
-- Data for Name: cart_items; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.cart_items (id, product_id, quantity, added_at, client_id) FROM stdin;
\.


--
-- TOC entry 3594 (class 0 OID 16460)
-- Dependencies: 218
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.categories (id, name, description) FROM stdin;
1	Médico General	Área de medicina general y consulta externa
2	Obstetra	Área de obstetricia y ginecología
3	Cirugía	Área de cirugía general y especializada
4	Pediatría	Área de atención pediátrica
5	Enfermería	Área de enfermería y cuidados
6	Emergencias	Área de emergencias y urgencias
7	Laboratorio	Área de laboratorio clínico
8	Radiología	Área de radiología e imágenes
\.


--
-- TOC entry 3621 (class 0 OID 16744)
-- Dependencies: 245
-- Data for Name: client_addresses; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.client_addresses (id, client_id, address, city, region, postal_code, phone, is_default, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 3619 (class 0 OID 16705)
-- Dependencies: 243
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.clients (id, name, email, phone, dni, gender, birth_date, status, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 3615 (class 0 OID 16668)
-- Dependencies: 239
-- Data for Name: coupons; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.coupons (id, code, description, discount_type, discount_value, max_uses, used_count, expires_at, created_at, is_active) FROM stdin;
\.


--
-- TOC entry 3596 (class 0 OID 16466)
-- Dependencies: 220
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.order_items (order_id, product_id, quantity, price) FROM stdin;
\.


--
-- TOC entry 3597 (class 0 OID 16469)
-- Dependencies: 221
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.orders (id, address_id, total_price, status, created_at, coupon_id, discount_amount, client_id) FROM stdin;
\.


--
-- TOC entry 3599 (class 0 OID 16475)
-- Dependencies: 223
-- Data for Name: payments; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.payments (id, order_id, method, status, paid_at, proof_url) FROM stdin;
\.


--
-- TOC entry 3601 (class 0 OID 16482)
-- Dependencies: 225
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permissions (id, role_id, table_name, can_create, can_read, can_update, can_delete) FROM stdin;
\.


--
-- TOC entry 3603 (class 0 OID 16492)
-- Dependencies: 227
-- Data for Name: product_categories; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.product_categories (id, name, description, created_at) FROM stdin;
1	Buzos	Buzos médicos y quirúrgicos	2025-07-01 04:25:18.734342+00
2	Pantalones	Pantalones médicos y quirúrgicos	2025-07-01 04:25:18.734342+00
3	Casacas	Casacas y batas médicas	2025-07-01 04:25:18.734342+00
4	Pijamas Quirúrgicas	Conjuntos completos para cirugía	2025-07-01 04:25:18.734342+00
5	Calzado	Zapatos y calzado médico especializado	2025-07-01 04:25:18.734342+00
6	Accesorios	Gorros, mascarillas y otros accesorios	2025-07-01 04:25:18.734342+00
7	Bolsos	Bolsos y maletines médicos	2025-07-01 04:25:18.734342+00
8	Calcetines	Calcetines de compresión y médicos	2025-07-01 04:25:18.734342+00
9	Ropa Interior	Ropa interior especializada	2025-07-01 04:25:18.734342+00
10	Uniformes Completos	Sets completos de uniformes	2025-07-01 04:25:18.734342+00
\.


--
-- TOC entry 3623 (class 0 OID 25006)
-- Dependencies: 247
-- Data for Name: product_category_mapping; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.product_category_mapping (id, product_id, category_id, product_category_id, created_at, updated_at) FROM stdin;
3	8	5	2	2025-07-02 00:19:01.756442+00	2025-07-02 16:09:18.46119
5	10	2	3	2025-07-02 00:19:01.756442+00	2025-07-02 16:09:18.46119
6	11	4	3	2025-07-02 00:19:01.756442+00	2025-07-02 16:09:18.46119
7	12	5	5	2025-07-02 00:19:01.756442+00	2025-07-02 16:09:18.46119
8	13	3	5	2025-07-02 00:19:01.756442+00	2025-07-02 16:09:18.46119
9	14	3	6	2025-07-02 00:19:01.756442+00	2025-07-02 16:09:18.46119
14	23	3	9	2025-07-02 15:52:13.826597+00	2025-07-02 16:09:42.551709
17	26	3	10	2025-07-02 16:39:10.463314+00	2025-07-02 16:49:56.866028
16	25	3	9	2025-07-02 16:34:48.582833+00	2025-07-02 16:54:17.667926
1	6	1	1	2025-07-02 00:19:01.756442+00	2025-07-02 16:55:11.353567
4	9	3	2	2025-07-02 00:19:01.756442+00	2025-07-02 16:55:55.303142
2	7	3	1	2025-07-02 00:19:01.756442+00	2025-07-02 16:59:39.580504
18	27	3	9	2025-07-02 16:57:19.176139+00	2025-07-02 20:26:04.74757
15	24	3	9	2025-07-02 16:19:17.648338+00	2025-07-02 20:26:16.985566
\.


--
-- TOC entry 3604 (class 0 OID 16495)
-- Dependencies: 228
-- Data for Name: product_images; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.product_images (id, product_id, image_url, created_at) FROM stdin;
\.


--
-- TOC entry 3606 (class 0 OID 16502)
-- Dependencies: 230
-- Data for Name: product_reviews; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.product_reviews (id, product_id, rating, comment, created_at, client_id) FROM stdin;
\.


--
-- TOC entry 3608 (class 0 OID 16510)
-- Dependencies: 232
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.products (id, name, description, price, stock, created_at, deleted_at, size, product_type_id, status, updated_at) FROM stdin;
26	PRUBE AADSD	DWADA	24.00	5	2025-07-02 16:39:10.463314+00	\N	XXL	\N	ACTIVE	2025-07-02 16:49:56.866028
8	Pantalón Médico Blanco	Pantalón médico clásico, talla S	45.00	42	2025-06-30 20:54:34.38014+00	\N	S	2	ACTIVE	2025-07-02 16:08:05.706477
10	Casaca Obstétrica Rosa	Casaca especializada para obstetricia	75.00	20	2025-06-30 20:54:34.38014+00	\N	M	3	ACTIVE	2025-07-02 16:08:05.706477
11	Casaca Pediátrica con Diseño	Casaca colorida para pediatría	72.00	18	2025-06-30 20:54:34.38014+00	\N	S	3	ACTIVE	2025-07-02 16:08:05.706477
12	Zapatos Blancos Enfermería	Calzado cómodo para largas jornadas	95.00	30	2025-06-30 20:54:34.38014+00	\N	UNIQUE	5	ACTIVE	2025-07-02 16:08:05.706477
13	Zuecos Antideslizantes	Zuecos para áreas húmedas	85.00	25	2025-06-30 20:54:34.38014+00	\N	UNIQUE	5	ACTIVE	2025-07-02 16:08:05.706477
14	Gorro Quirúrgico Desechable	Pack de 50 gorros desechables	15.00	100	2025-06-30 20:54:34.38014+00	\N	UNIQUE	6	ACTIVE	2025-07-02 16:08:05.706477
25	prubeadasd	dasdasGG	232.00	10	2025-07-02 16:34:48.582833+00	\N	UNIQUE	\N	ACTIVE	2025-07-02 16:54:17.667926
6	Buzo Médico Azul Marino{	Buzo médico talla M, manga larga	65.00	2	2025-06-30 20:54:34.38014+00	\N	M	1	ACTIVE	2025-07-02 16:55:11.353567
9	Pantalón Quirúrgico Celeste	Pantalón para cirugía, talla XL	48.00	2	2025-06-30 20:54:34.38014+00	\N	XL	2	ACTIVE	2025-07-02 16:55:55.303142
7	Buzo Quirúrgico VerdeEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE	Buzo para área quirúrgica, talla L	68.00	20	2025-06-30 20:54:34.38014+00	\N	L	1	COMING_SOON	2025-07-02 16:59:39.580504
27	Pantalón Quirúrgico CelesteDSD	SDSDS	48.00	34	2025-07-02 16:57:19.176139+00	\N	UNIQUE	\N	ACTIVE	2025-07-03 03:05:04.927585
23	Prueba Edicion	wewr	23.00	12	2025-07-02 15:52:13.826597+00	\N	UNIQUE	\N	ACTIVE	2025-07-03 03:05:21.010629
24	Prueba Productoq 2	daddasd	23.00	10	2025-07-02 16:19:17.648338+00	\N	UNIQUE	\N	INACTIVE	2025-07-03 03:05:53.735131
\.


--
-- TOC entry 3617 (class 0 OID 16690)
-- Dependencies: 241
-- Data for Name: review_images; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.review_images (id, review_id, image_url, uploaded_at) FROM stdin;
\.


--
-- TOC entry 3610 (class 0 OID 16518)
-- Dependencies: 234
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.roles (id, name, description) FROM stdin;
1	ADMIN	Acceso total al sistema, incluyendo la gestión de usuarios, productos, pedidos y configuración
2	SELLER	Usuario con permisos para gestionar productos y pedidos
3	MODERATOR	Revisa y aprueba contenidos, gestiona reseñas de productos
4	SUPPORT	Atiende consultas de clientes, gestiona devoluciones y reclamos
5	INVENTORY	Gestiona el stock y entradas/salidas de productos
\.


--
-- TOC entry 3612 (class 0 OID 16531)
-- Dependencies: 236
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.users (id, username, password, email, name, status, created_by, updated_by, created_at, updated_at, role_id) FROM stdin;
28	Yhamitadsadas	$2y$10$bv0PuwhGpb9sTZ5z0H.NNOKAi2QJ0ummsdj5o3tMJ9iSI7Lbd1Fhi	dwq@gmail.com	Yham Asencio Cuenta	INACTIVE	system	system	2025-06-30 15:32:49.090461+00	2025-07-02 00:55:54.878713+00	5
29	dwqw	$2y$10$5yNHqUEgK2yLPdbPFbj9F.tcYOMORHcln9yRE265N.G3weeIsCK9e	dqwdwq@gmail.com	WALTER MANUEL NAVARRO ARELLANA	ACTIVE	system	system	2025-07-02 01:28:26.296482+00	2025-07-02 01:28:26.296482+00	1
30	wqdwqd	$2y$10$f3F1td2hMxnh6xiUw2yvieHC6cF2u3uXie0sNxo8nxhj/OiogrmDq	wddwdwwdw@gmail.com	wdadwa	INACTIVE	system	system	2025-07-02 01:28:41.506892+00	2025-07-02 01:28:41.506892+00	1
31	fdqw	$2y$10$HuZaenmiLJGxzaMlPjIr7uiW4wJvS4AcgKVOvFekhuwV97.aSYAOO	dwqdwqdwad@gmail.com	dwadwa	INACTIVE	system	system	2025-07-02 01:28:59.507884+00	2025-07-02 01:28:59.507884+00	1
32	sada	$2y$10$/VTdfpSsGVbD9IzP9Jcb2.RRQkWmIVfdmjVSJJISAJWl9cxahxEci	dasd@gmail.com	adwadawd	INACTIVE	system	system	2025-07-02 01:29:25.044502+00	2025-07-02 01:29:25.044502+00	1
33	adwd	$2y$10$0Rtj9Pb/EmAN/87yYRrIB.EvKsjHsCv1wT83k1BADS47AYHsyDj3e	wadwadggwadwadwadwhh@gmail.com	wdwad	ACTIVE	system	system	2025-07-02 01:29:49.575043+00	2025-07-02 15:55:58.253235+00	1
24	soporte1	soportepass1	soporte1@example.com	Soporte Uno	INACTIVE	system	system	2025-06-29 08:05:12.149661+00	2025-06-30 14:32:27.718226+00	4
22	vendedor1	sellerpass1	seller1@example.com	Vendedor Uno	INACTIVE	system	system	2025-06-29 08:05:12.149661+00	2025-06-30 14:32:33.866494+00	2
23	moderador1	modpass1	mod1@example.com	Moderador Uno	ACTIVE	system	system	2025-06-29 08:05:12.149661+00	2025-06-30 14:32:45.096686+00	5
21	admin1	adminpass1	admin1@example.com	Admin Uno	INACTIVE	system	system	2025-06-29 08:05:12.149661+00	2025-06-30 14:44:39.768102+00	1
25	inventario12	invpass1	inv1@example.com	Inventario Uno	INACTIVE	system	system	2025-06-29 08:05:12.149661+00	2025-06-30 14:46:39.492287+00	5
27	DanielitoRios213	$2y$10$Y3zOosaZ8yFyOUQVSUbov.AHscCGZO9Dn6yzQzmguUH7jat.w4HzC	rios1232@gmail.com	JeanCarlos Daniel Rios Gonzales	INACTIVE	system	system	2025-06-30 14:33:35.975403+00	2025-07-01 04:49:07.586967+00	1
26	0333211027ddd	$2y$10$wPr9iIXjImpv0zPXOknOyeuyvmuhGXkthHLM8OMmWJo7iR8u46zWq	carlossipanlozano@gmail.com	Carlos Jesus Sipan Lozano	ACTIVE	system	system	2025-06-30 14:10:47.276375+00	2025-07-01 05:02:45.489144+00	1
\.


--
-- TOC entry 3647 (class 0 OID 0)
-- Dependencies: 215
-- Name: audit_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.audit_log_id_seq', 1, false);


--
-- TOC entry 3648 (class 0 OID 0)
-- Dependencies: 217
-- Name: cart_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.cart_items_id_seq', 1, false);


--
-- TOC entry 3649 (class 0 OID 0)
-- Dependencies: 219
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.categories_id_seq', 8, true);


--
-- TOC entry 3650 (class 0 OID 0)
-- Dependencies: 244
-- Name: client_addresses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.client_addresses_id_seq', 1, false);


--
-- TOC entry 3651 (class 0 OID 0)
-- Dependencies: 242
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.clients_id_seq', 1, false);


--
-- TOC entry 3652 (class 0 OID 0)
-- Dependencies: 238
-- Name: coupons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.coupons_id_seq', 1, false);


--
-- TOC entry 3653 (class 0 OID 0)
-- Dependencies: 222
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.orders_id_seq', 2, true);


--
-- TOC entry 3654 (class 0 OID 0)
-- Dependencies: 224
-- Name: payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.payments_id_seq', 1, true);


--
-- TOC entry 3655 (class 0 OID 0)
-- Dependencies: 226
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permissions_id_seq', 1, false);


--
-- TOC entry 3656 (class 0 OID 0)
-- Dependencies: 246
-- Name: product_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.product_categories_id_seq', 10, true);


--
-- TOC entry 3657 (class 0 OID 0)
-- Dependencies: 248
-- Name: product_category_mapping_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.product_category_mapping_id_seq', 18, true);


--
-- TOC entry 3658 (class 0 OID 0)
-- Dependencies: 229
-- Name: product_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.product_images_id_seq', 1, false);


--
-- TOC entry 3659 (class 0 OID 0)
-- Dependencies: 231
-- Name: product_reviews_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.product_reviews_id_seq', 3, true);


--
-- TOC entry 3660 (class 0 OID 0)
-- Dependencies: 233
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.products_id_seq', 27, true);


--
-- TOC entry 3661 (class 0 OID 0)
-- Dependencies: 240
-- Name: review_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.review_images_id_seq', 1, false);


--
-- TOC entry 3662 (class 0 OID 0)
-- Dependencies: 235
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.roles_id_seq', 5, true);


--
-- TOC entry 3663 (class 0 OID 0)
-- Dependencies: 237
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.users_id_seq', 33, true);


--
-- TOC entry 3368 (class 2606 OID 16554)
-- Name: audit_log audit_log_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.audit_log
    ADD CONSTRAINT audit_log_pkey PRIMARY KEY (id);


--
-- TOC entry 3370 (class 2606 OID 16556)
-- Name: cart_items cart_items_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_pkey PRIMARY KEY (id);


--
-- TOC entry 3372 (class 2606 OID 16560)
-- Name: categories categories_name_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_name_key UNIQUE (name);


--
-- TOC entry 3374 (class 2606 OID 16562)
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- TOC entry 3420 (class 2606 OID 16754)
-- Name: client_addresses client_addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.client_addresses
    ADD CONSTRAINT client_addresses_pkey PRIMARY KEY (id);


--
-- TOC entry 3414 (class 2606 OID 16719)
-- Name: clients clients_dni_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_dni_key UNIQUE (dni);


--
-- TOC entry 3416 (class 2606 OID 16717)
-- Name: clients clients_email_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_email_key UNIQUE (email);


--
-- TOC entry 3418 (class 2606 OID 16715)
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 3408 (class 2606 OID 16682)
-- Name: coupons coupons_code_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_code_key UNIQUE (code);


--
-- TOC entry 3410 (class 2606 OID 16680)
-- Name: coupons coupons_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_pkey PRIMARY KEY (id);


--
-- TOC entry 3376 (class 2606 OID 16564)
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (order_id, product_id);


--
-- TOC entry 3378 (class 2606 OID 16566)
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- TOC entry 3380 (class 2606 OID 16568)
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- TOC entry 3382 (class 2606 OID 16570)
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 3384 (class 2606 OID 16572)
-- Name: permissions permissions_role_id_table_name_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_role_id_table_name_key UNIQUE (role_id, table_name);


--
-- TOC entry 3386 (class 2606 OID 25005)
-- Name: product_categories product_categories_name_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_name_key UNIQUE (name);


--
-- TOC entry 3388 (class 2606 OID 25003)
-- Name: product_categories product_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 3422 (class 2606 OID 25011)
-- Name: product_category_mapping product_category_mapping_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_category_mapping
    ADD CONSTRAINT product_category_mapping_pkey PRIMARY KEY (id);


--
-- TOC entry 3424 (class 2606 OID 25013)
-- Name: product_category_mapping product_category_mapping_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_category_mapping
    ADD CONSTRAINT product_category_mapping_unique UNIQUE (product_id, category_id, product_category_id);


--
-- TOC entry 3390 (class 2606 OID 16576)
-- Name: product_images product_images_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_pkey PRIMARY KEY (id);


--
-- TOC entry 3392 (class 2606 OID 16578)
-- Name: product_reviews product_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_pkey PRIMARY KEY (id);


--
-- TOC entry 3394 (class 2606 OID 16737)
-- Name: product_reviews product_reviews_product_id_client_id_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_product_id_client_id_key UNIQUE (product_id, client_id);


--
-- TOC entry 3396 (class 2606 OID 16582)
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- TOC entry 3412 (class 2606 OID 16698)
-- Name: review_images review_images_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.review_images
    ADD CONSTRAINT review_images_pkey PRIMARY KEY (id);


--
-- TOC entry 3398 (class 2606 OID 16584)
-- Name: roles roles_name_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_key UNIQUE (name);


--
-- TOC entry 3400 (class 2606 OID 16586)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 3402 (class 2606 OID 16590)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3404 (class 2606 OID 16592)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3406 (class 2606 OID 16594)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 3446 (class 2620 OID 16760)
-- Name: client_addresses trg_update_client_addresses; Type: TRIGGER; Schema: public; Owner: admin
--

CREATE TRIGGER trg_update_client_addresses BEFORE UPDATE ON public.client_addresses FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3445 (class 2620 OID 16720)
-- Name: clients trg_update_clients_updated_at; Type: TRIGGER; Schema: public; Owner: admin
--

CREATE TRIGGER trg_update_clients_updated_at BEFORE UPDATE ON public.clients FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3443 (class 2620 OID 25047)
-- Name: products trg_update_product_status; Type: TRIGGER; Schema: public; Owner: admin
--

CREATE TRIGGER trg_update_product_status BEFORE UPDATE ON public.products FOR EACH ROW EXECUTE FUNCTION public.update_product_status();


--
-- TOC entry 3444 (class 2620 OID 16595)
-- Name: users trg_update_users_updated_at; Type: TRIGGER; Schema: public; Owner: admin
--

CREATE TRIGGER trg_update_users_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3425 (class 2606 OID 16726)
-- Name: cart_items cart_items_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_client_id_fkey FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- TOC entry 3426 (class 2606 OID 16596)
-- Name: cart_items cart_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- TOC entry 3439 (class 2606 OID 16755)
-- Name: client_addresses client_addresses_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.client_addresses
    ADD CONSTRAINT client_addresses_client_id_fkey FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- TOC entry 3427 (class 2606 OID 16606)
-- Name: order_items order_items_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- TOC entry 3428 (class 2606 OID 16611)
-- Name: order_items order_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id);


--
-- TOC entry 3429 (class 2606 OID 16766)
-- Name: orders orders_address_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_address_id_fkey FOREIGN KEY (address_id) REFERENCES public.client_addresses(id) ON DELETE SET NULL;


--
-- TOC entry 3430 (class 2606 OID 16721)
-- Name: orders orders_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_client_id_fkey FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- TOC entry 3431 (class 2606 OID 16684)
-- Name: orders orders_coupon_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_coupon_id_fkey FOREIGN KEY (coupon_id) REFERENCES public.coupons(id);


--
-- TOC entry 3432 (class 2606 OID 16626)
-- Name: payments payments_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id);


--
-- TOC entry 3433 (class 2606 OID 16631)
-- Name: permissions permissions_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 3440 (class 2606 OID 25021)
-- Name: product_category_mapping product_category_mapping_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_category_mapping
    ADD CONSTRAINT product_category_mapping_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- TOC entry 3441 (class 2606 OID 25026)
-- Name: product_category_mapping product_category_mapping_product_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_category_mapping
    ADD CONSTRAINT product_category_mapping_product_category_id_fkey FOREIGN KEY (product_category_id) REFERENCES public.product_categories(id) ON DELETE CASCADE;


--
-- TOC entry 3442 (class 2606 OID 25016)
-- Name: product_category_mapping product_category_mapping_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_category_mapping
    ADD CONSTRAINT product_category_mapping_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- TOC entry 3434 (class 2606 OID 16646)
-- Name: product_images product_images_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- TOC entry 3435 (class 2606 OID 16731)
-- Name: product_reviews product_reviews_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_client_id_fkey FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- TOC entry 3436 (class 2606 OID 16651)
-- Name: product_reviews product_reviews_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- TOC entry 3438 (class 2606 OID 16699)
-- Name: review_images review_images_review_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.review_images
    ADD CONSTRAINT review_images_review_id_fkey FOREIGN KEY (review_id) REFERENCES public.product_reviews(id) ON DELETE CASCADE;


--
-- TOC entry 3437 (class 2606 OID 16738)
-- Name: users users_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(id);


-- Completed on 2025-07-03 04:08:31 UTC

--
-- PostgreSQL database dump complete
--

