CREATE TABLE unknown_users 
(
ip_address varchar(50),
GET_book_tags_count int					DEFAULT 0,		 		-- corresponds to GET /book_tags/{book_tag}.json
GET_lc_numbers_count int				DEFAULT 0,				-- corresponds to GET /lc_numbers/{call_number}.json
POST_institutions_count int				DEFAULT 0,				-- corresponds to POST /institutions/
GET_insitutions_count int				DEFAULT 0,				-- corresponds to GET /institutions/
POST_institutions_edit_count int		DEFAULT 0,				-- corresponds to POST /institutions/edit
GET_institutions_specific_count int		DEFAULT 0,				-- corresponds to GET /institutions/{inst_id}.json
GET_institutions_available_count int	DEFAULT 0,				-- corresponds to GET /institutions/available/{inst_id}.json
GET_users_count int						DEFAULT 0,				-- corresponds to GET /users
POST_users_count int					DEFAULT 0,				-- corresponds to POST /users
POST_users_edit_count int				DEFAULT 0,				-- corresponds to POST /users/edit
GET_users_permissions_count int			DEFAULT 0,				-- corresponds to GET /users/{user_id}/permissions
POST_users_persmissions_count int		DEFAULT 0,				-- corresponds to POST /users/{user_id}/permissions
GET_users_specific_count int			DEFAULT 0,				-- corresponds to GET /users/{user_id}.json
GET_users_available_count int			DEFAULT 0,				-- corresponds to GET /users/available/{user_id}.json
GET_make_tags_count int					DEFAULT 0,				-- corresponds to GET /make_tags/{paper_type}.pdf
GET_paper_formats_count int				DEFAULT 0,				-- corresponds to GET /make_tags/paper_formats
GET_oauth_request_token_count int		DEFAULT 0,				-- corresponds to GET /oauth/get_request_token
GET_oauth_login_count int				DEFAULT 0,				-- corresponds to GET /oauth/login
GET_oauth_access_token_count int		DEFAULT 0,				-- corresponds to GET /oauth/get_access_token
GET_oauth_whoami_count int				DEFAULT 0,				-- corresponds to GET /oauth/whoami
last_reset datetime						DEFAULT NULL	
);


-----------------------------------------------------------------
-- Moving on to users table -------------------------------------

ALTER TABLE users 
ADD COLUMN POST_book_pings_count int 			DEFAULT 0,			-- corresponds to POST /book_pings/
ADD COLUMN GET_book_pings_count int				DEFAULT 0,			-- corresponds to GET /book_pings/
ADD COLUMN GET_book_pings_count_count int		DEFAULT 0,			-- corresponds to GET /book_pings/count
ADD COLUMN GET_book_pings_specific_count int	DEFAULT 0,			-- corresponds to GET /book_pings/{book_ping_id}.json
ADD COLUMN last_reset datetime					DEFAULT NULL
;
