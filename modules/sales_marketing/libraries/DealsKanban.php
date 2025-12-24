<?php

namespace modules\sales_marketing\libraries;

use app\services\AbstractKanban;

class DealsKanban extends AbstractKanban
{
    protected function table(): string
    {
        return '_sam';
    }

    public function defaultSortDirection()
    {
        return (!empty(get_option('default_sam_kanban_sort_by')) ? get_option('default_sam_kanban_sort_by') : 'dealorder');

    }

    public function defaultSortColumn()
    {
        return (!empty(get_option('default_sam_kanban_sort_type')) ? get_option('default_sam_kanban_sort_type') : 'asc');
    }

    public function limit()
    {
        return (!empty(get_option('sam_kanban_limit')) ? get_option('sam_kanban_limit') : 20);
    }

    protected function applySearchQuery($q): self
    {
        if (!startsWith($q, '#')) {
            $q = $this->ci->db->escape_like_str($this->q);
            $this->ci->db->where('(' . db_prefix() . '_sam.title LIKE "%' . $q . '%" ESCAPE \'!\' OR tbl_sam_stages.stage_name LIKE "%' . $q . '%" ESCAPE \'!\' OR tbl_sam_source.source_name LIKE "%' . $q . '%" ESCAPE \'!\' OR tbl_sam_pipelines.pipeline_name LIKE "%' . $q . '%" ESCAPE \'!\' OR deal_value LIKE "%' . $q . '%" ESCAPE \'!\' OR CONCAT(' . db_prefix() . 'staff.firstname, \' \', ' . db_prefix() . 'staff.lastname) LIKE "%' . $q . '%" ESCAPE \'!\')');
        } else {
            $this->ci->db->where(db_prefix() . '_sam.id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->ci->db->escape_str(strafter($q, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'sam\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
        }

        return $this;
    }

    protected function initiateQuery(): self
    {
        $this->ci->db->select('tbl_sam.*,tbl_sam_stages.stage_name,tbl_sam_source.source_name,tbl_sam_pipelines.pipeline_name,(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . '_sam.id and rel_type="sam" ORDER by tag_order ASC) as tags,
        (SELECT COUNT(id) FROM ' . db_prefix() . 'files WHERE rel_id=' . db_prefix() . '_sam.id AND rel_type="sam") as total_files,
        (SELECT COUNT(id) FROM ' . db_prefix() . 'tasks WHERE rel_id=' . db_prefix() . '_sam.id AND rel_type="sam") as total_tasks,
        (SELECT COUNT(calls_id) FROM tbl_sam_calls WHERE module_field_id=' . db_prefix() . '_sam.id) as total_calls,
        (SELECT COUNT(mettings_id) FROM tbl_sam_mettings WHERE module_field_id=tbl_sam.id) as total_mettings,
        (SELECT COUNT(id) FROM tbl_sam_comments WHERE deal_id=' . db_prefix() . '_sam.id) as total_comments,
        (SELECT COUNT(id) FROM tbl_sam_email WHERE deals_id=' . db_prefix() . '_sam.id) as total_emails,
        (SELECT COUNT(items_id) FROM tbl_sam_items WHERE deals_id=' . db_prefix() . '_sam.id) as total_items,
        (SELECT COUNT(id) FROM tbl_sam_activity_log WHERE deal_id=' . db_prefix() . '_sam.id) as total_activity_log');
        $this->ci->db->from('tbl_sam');
        $this->ci->db->join('tbl_sam_stages', 'tbl_sam_stages.stage_id=tbl_sam.stage_id', 'left');
        $this->ci->db->join('tbl_sam_source', 'tbl_sam_source.source_id=tbl_sam.source_id', 'left');
        $this->ci->db->join('tbl_sam_pipelines', 'tbl_sam_pipelines.pipeline_id=tbl_sam.pipeline_id', 'left');
        $this->ci->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . '_sam.default_deal_owner', 'left');
        $this->ci->db->where('tbl_sam.stage_id', $this->status);

        if (!has_permission('sam', '', 'view')) {
            $this->ci->db->where('(default_deal_owner = ' . get_staff_user_id() . ')');
        }

        return $this;
    }
}
